# En

## TreeAlgorithms

This group of methods for work with tree structure. Provide methods build tree from flat array, find node in the tree by id, walk tree and execulte callback function for each node. Node of the tree has fields like as `id`, `parent_id`, `children`. Concrete names of the tree node fields can be configured with TreeAlgorithms properties `idFieldName`, `parentIdFieldName`, `childsFieldName`. Also exists javascript release https://github.com/lamzin-andrey/landlib#treealgorithms. Interfaces of javascript object and this php class is full compatible.

### getBranchIdList($node)

Resursive walk all children nodes of the node and return array of integer with `TreeAlgorithms::$idFieldName` values.

In example

```php
	$sFieldName = TreeAlgorithms::$idFieldName;
	$id = $node->$sFieldName; 
```

`$id` containts into result array in the zero position.


### walkAndExecuteAction($oTree, $oCallback)

Walk all nodes $oTree and execute callback for each node. Node pass as argument to the callback.

$oCallback must be object 

```php
//... in your class method body ...

$oCallback = new StdClass();
$oCallback->context = $this; //or static
$oCallback->f = 'yourClassMethodName';
$oCallback->isStatic = true; //or false if need static class

```

You can not set `isStatic` field if context will pointer on the class object ($this).

### buildTreeFromFlatList($aScopeArgs, $bSetChildsAsArray = false)

Build tree from "flat" `array` argument `aScopeArgs`.

For example, $aScopeArgs can be like:

```php
$aFlatList = [
	[
		'id' => 1,
		'name' =>  'Books',
		'parent' => 0
	],
	[
		'id' => 2,
		'name' => 'Sciences',
		'parent' => 1
	],
	[
		'id' => 3,
		'name' => 'Adventure',
		'parent' => 1
	],
	[
		'id' => 4,
		'name' => 'Computer Sciences',
		'parent' => 2
	]
];
```

Then code:

```php
TreeAlgorithms::$parentIdFieldName = 'parent';
TreeAlgorithms::$childsFieldName = 'inners';
$aTrees = TreeAlgorithms::buildTreeFromFlatList($aFlatList);
//$aTrees[0] will containt our tree
```

return array:
```php
[
	[
		'id' => 1,
		'name' => "Books",
		'parent' => 0,
		'inners' => {//(StdClass)
			'2' => [
				'id' => 2,
				'name' => 'Sciences',
				'parent' => 1,
				'inners' => {//(StdClass)
					'4' => [
						'id' => 4,
						'name' => 'Computer Sciences',
						'parent' => 2
					]
				}
			],
			'3' => [
				'id' => 3,
				'name' => 'Adventure',
				'parent' => 1
			]
		}
	]
]
```

If you pass second argument `TreeAlgorithms.buildTreeFromFlatList(aFlatList, true)` result will be:

```php
[
	[
		'id' => 1,
		'name' => "Books",
		'parent' => 0,
		'inners' => [
			'2' => [
				'id' => 2,
				'name' => 'Sciences',
				'parent' => 1,
				'inners' => [
					'4' => [
						'id' => 4,
						'name' => 'Computer Sciences',
						'parent' => 2
					]
				]
			],
			'3' => [
				'id' => 3,
				'name' => 'Adventure',
				'parent' => 1
			]
		]
	]
]
```

### findById($oTree, $id)

Resursive search node in the all childs of the oTree (oNode). Each node will check as 

```php
$str = TreeAlgorithms::$idFieldName;
$node->$str == $id
```

Return null or founded node;

### remove($oTree, $id)

Search node by id (see findById method), search parent node and remove node from parent node.


### getNodesByNodeId($oTree, $id)

Return array of nodes from tree root to node with id = id


# Ru

## Что это

Это группа методов для работы с древовидной структурой. Каждый элемент дерева должен иметь поля, такие как `id, parent_id, children`.
Конкретные имена полей могут конфигурироваться перед запуском методов `TreeAlgorithms` путём изменения значений свойств `idFieldName, parentIdFieldName, childsFieldName`.

### getBranchIdList($node)

Рекурсивно обходит дерево и собирает идентификаторы элементов в один массив. В качестве идентификатора элмента используется его поле с именем, заданным в TreeAlgorithms.idFieldName. Идентификатор аргумента `$node` `id` из примера

```php
	$sFieldName = static::$idFieldName;
	$id = $node->$sFieldName; 
```

также будет содержаться в результате обхода, он будет в нулевой позиции массива.


### walkAndExecuteAction($oTree, $oCallback)

Рекурсивно обходит дерево и для каждого элемента выполняет вызов колбэка. В колбэк передаётся элемент как аргумент.

oCallback должен быть объектом

```php
//... in your class method body ...

$oCallback = new StdClass();
$oCallback->context = $this; //or static
$oCallback->f = 'yourClassMethodName';
$oCallback->isStatic = true; //or false if need static class

```

Вы можете не устанавливать поле isStatic если передаёте в контексте указатель на экземпляр класса.

### buildTreeFromFlatList($aScopeArgs, $bSetChildsAsArray = false)

Получает плоский список `Array` из `aScopeArgs` и строит из него дерево.

Например, aScopeArgs может быть таким:

```php
$aFlatList = [
	[
		'id' => 1,
		'name' =>  'Books',
		'parent' => 0
	],
	[
		'id' => 2,
		'name' => 'Sciences',
		'parent' => 1
	],
	[
		'id' => 3,
		'name' => 'Adventure',
		'parent' => 1
	],
	[
		'id' => 4,
		'name' => 'Computer Sciences',
		'parent' => 2
	]
];
```

Тогда код:

```php
TreeAlgorithms::$parentIdFieldName = 'parent';
TreeAlgorithms::$childsFieldName = 'inners';
$aTrees = TreeAlgorithms::buildTreeFromFlatList($aFlatList);
//$aTrees[0] will containt our tree
```

вернет массив:
```php
[
	[
		'id' => 1,
		'name' => "Books",
		'parent' => 0,
		'inners' => {//(StdClass)
			'2' => [
				'id' => 2,
				'name' => 'Sciences',
				'parent' => 1,
				'inners' => {//(StdClass)
					'4' => [
						'id' => 4,
						'name' => 'Computer Sciences',
						'parent' => 2
					]
				}
			],
			'3' => [
				'id' => 3,
				'name' => 'Adventure',
				'parent' => 1
			]
		}
	]
]
```

Если вы передадите вторым аргументом истину `TreeAlgorithms.buildTreeFromFlatList(aFlatList, true)` результат будет:

```php
[
	[
		'id' => 1,
		'name' => "Books",
		'parent' => 0,
		'inners' => [
			'2' => [
				'id' => 2,
				'name' => 'Sciences',
				'parent' => 1,
				'inners' => [
					'4' => [
						'id' => 4,
						'name' => 'Computer Sciences',
						'parent' => 2
					]
				]
			],
			'3' => [
				'id' => 3,
				'name' => 'Adventure',
				'parent' => 1
			]
		]
	]
]
```

### findById($oTree, $id)

Рекурсивно обходит дерево и для каждого элемента сравнивает свойство, заданное в `TreeAlgorithms.idFieldName` с `id`. Если сравнение


```php
$str = TreeAlgorithms::$idFieldName;
$node->$str == $id
```

оказалось истинным, вернёт найденный элемент, иначе null.

### remove($oTree, $id)

Ищет элемент по id используя findById, если он найден ищет для него родительский элемент и удаляет из массива потомков найденный по id элемент.


### getNodesByNodeId($oTree, $id)

Возвращает массив объектов (элементов дерева) от корня до узла с id = id
