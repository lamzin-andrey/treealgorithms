<?php
namespace Landlib;
class TreeAlgorithms {
	/** @property strings idFieldName */
	static public $idFieldName = 'id';

	/** @property string parentIdFieldName */
	static public $parentIdFieldName = 'parent_id';

	/** @property string childsFieldName */
	static public $childsFieldName = 'children';
	
	/**
	 * @description Get all "$this->idFieldName" values from node and all node childs (all levels)
	 * @param StdClass node 
	 * @return array of "$this->idFieldName" nodes  (all levels)
	*/
	static public function getBranchIdList($node)
	{
		$r = [];
		$sField = static::$idFieldName;
		$r[] = $node->$sField;
		
		$sFieldName = static::$childsFieldName;
		if (isset($node->$sFieldName)) {
			$part = [];
			foreach ($node->$sFieldName as $oItem) {
				$part = static::getBranchIdList($oItem);
				for ($j = 0; $j < count($part); $j++) {
					$r[] = $part[$j];
				}
			}
		}
		return $r;
	}
	/**
	 * @description walking oTree and execute oCallback(currentNode)
	 * @param StdClass &$oTree
	 * @param StdClass $oCallback  {context, f:function, isStatic:bool}
	*/
	static public function walkAndExecuteAction(&$oTree, $oCallback)
	{
		//oCallback.f.call(oCallback.context, oTree);
		$sFunction = $oCallback->f;
		if (isset($oCallback->isStatic) && $oCallback->isStatic == true) {
			$oCallback->context::$sFunction($oTree);
		} else {
			$oCallback->context->$sFunction($oTree);
		}
		$sFieldName = static::$childsFieldName;
		if (isset($oTree->$sFieldName)) {
			foreach ($oTree->$sFieldName as $oItem) {
				static::walkAndExecuteAction($oItem, $oCallback);
			}
		}
	}
	/**
	 * TODO ещё рахз пройти, пусть все будут объектами, сейчас тут явно лажа
	 * @description build tree from flat list
	 * @param StdClass aScopesArg array of objects {this.idFieldName, this.parentIdFieldName}
	 * @param bool bSetChildsAsArray = false if true, all 'children' (this.childsFieldName) property will convert to array
	 * @return array with root nodes in items
	*/
	static public function buildTreeFromFlatList($aScopes, $bSetChildsAsArray = false)
	{
		//let aBuf, nId, oItem, sChilds, oParent, a, r = [], i;
		$r = [];
		$aBuf = [];
		$sIdFieldName = static::$idFieldName;
		$sChildsFieldName = static::$childsFieldName;
		$sParentIdFieldName = static::$parentIdFieldName;
		foreach ($aScopes as $aItem) {
			if (is_array($aItem)) {
				$oItem = (object)$aItem;
			} else {
				$oItem = &$aItem;
			}
			if (!isset($oItem->$sParentIdFieldName)) {
				$oItem->$sParentIdFieldName = 0;
			}
			$nId = $oItem->$sIdFieldName;
			$aBuf[$nId] = $oItem;
			$aBuf[$nId]->$sChildsFieldName = [];
		}
		$aScopes = $aBuf;
		
		//тут строим дерево
		$sChilds = static::$childsFieldName;
		
		foreach ($aScopes as $nId => $oItem) {
			$oItem->$sIdFieldName = intval($oItem->$sIdFieldName);
			$oItem->$sParentIdFieldName = intval($oItem->$sParentIdFieldName);
			
			//перемещаем вложенные во внутрь
			if ($oItem->$sParentIdFieldName > 0) {
				$oParent = isset($aScopes[$oItem->$sParentIdFieldName]) ? $aScopes[$oItem->$sParentIdFieldName] : null;
				if ($oParent) {
					if (!$oParent->$sChilds) {
						$oParent->$sChilds = [];
					}
					//a = &oParent->sChilds;
					$a = &$oParent->$sChilds;
					$a[$nId] = $oItem;
					//aScopes[nId] = &a[nId];
					$aScopes[$nId] = &$a[$nId];
					$aScopes[$nId]->isMoved = true;
				}
			}
		}
		
		//удаляем из корня ссылки на перемещенные в родителей.
		foreach ($aScopes as $nId => $oItem) {
			if (isset($oItem->isMoved) && $oItem->isMoved) {
				unset($aScopes[$nId]);
			}
		}
		foreach ($aScopes as $nId => $oItem) {
			if ($bSetChildsAsArray) {
				$oCallback = new StdClass();
				$oCallback->context = 'TreeAlgorithms';
				$oCallback->isStatic= true;
				$oCallback->f = '_convertChildsToArray';
				static::walkAndExecuteAction($oItem, $oCallback);
			}
			$r[] = $oItem;
		}
		return $r;
	}
	/**
	 * @description Convert childs to array
	 * @param StdClass &$node 
	*/
	static private function _convertChildsToArray(&$node)
	{
		$newChilds = [];
		$sChldFieldName = static::$childsFieldName;
		foreach ($node->$sChldFieldName as $k => $oItem) {			
			$newChilds[] = $oItem;
		}
		$node->$sChldFieldName = $newChilds;
	}
	/**
	 * @description Find nodt By Id
	 * @param StdClass node (or tree)
	 * @param string id
	 * @return StdClass node or null
	*/
	static public function findById($node, $id)
	{
		$sIdFieldName = static::$idFieldName;
		if ($node->$sIdFieldName == $id) {
			return $node;
		}
		$sChildsFieldName = static::$childsFieldName;
		if ($node->$sChildsFieldName) {
			foreach ($node->$sChildsFieldName as $currNode) {
				$r = static::findById($currNode, $id);
				if ($r) {
					return $r;
				}
			}
		}
		return null;
	}
	/**
	 * @description Remove node from tree by node id
	 * @param StdClass tree (or tree)
	 * @param string id
	 * @return bool
	*/
	static public function remove($tree, $id)
	{
		$node = static::findById($tree, $id);
		if (!$node) {
			return false;
		}
		
		$sParentIdFieldName = static::$parentIdFieldName;
		$sIdFieldName        = static::$idFieldName;
		$sChildsFieldName   = static::$childsFieldName;
		
		$parentNode = null;
		if ($node->$sParentIdFieldName) {
			$parentNode = static::findById($tree, $node->$sParentIdFieldName);
		}
		if (!$parentNode || !$parentNode->$sChildsFieldName) {
			return false;
		}
		foreach ($parentNode->$sChildsFieldName as $i => $oItem) {
			if ($oItem->$sIdFieldName == $node->$sIdFieldName) {
				//delete parentNode[this.childsFieldName][i];
				unset($parentNode->$sChildsFieldName[$i]);
				//delete node; - in js trow error
				return true;
			}
		}
		return false;
	}
	/**
	 * @description Return array of nodes from tree root to node with id = nId
	 * @param StdClass oNode 
	 * @param int nId 
	 * @return array
	*/
	static public function getNodesByNodeId($oNode, $nId)
	{
		$sIdFieldName = static::$idFieldName;
		$sParentIdFieldName = static::$parentIdFieldName;
		$result = [];
		$node = static::findById($oNode, $nId);
		if ($node) {
			$result[] = $node;
			while ($node->$sParentIdFieldName) {
				$node = static::findById($oNode, $node->$sParentIdFieldName);
				if ($node) {
					$result[] = $node;
				} else {
					break;
				}
			}
			return array_reverse($result); 
		}
		return $result;
	}
}
