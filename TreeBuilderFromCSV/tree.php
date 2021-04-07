<?php

class TreeFromCSV
{
    function readTree($path): array
    {
        $tree = [];
        if (file_exists($path)) {
            if (is_readable($path)) {
                if (filesize($path) > 0) {
                    try {
                        $rows = array_map('str_getcsv', file($path));
                        $header = array_shift($rows);
                        $header = array_map('trim', $header);
                        foreach ($rows as $row) {
                            $tree[] = array_combine($header, $row);
                        }
                    } catch (Exception $e) {
                        echo $e->getTraceAsString();
                    }
                } else {
                    echo "\nThe file \"" . $path . "\" is empty.\n";
                }
            } else {
                echo "\nThe file \"" . $path . "\" is not readable.\n";
            }
        } else {
            echo "\nThe file \"" . $path . "\" was not found.\n";
        }
        return $tree;
    }

    function buildTree(array $tree, $parentId = 0): array
    {
        $branch = [];
        if (is_array($tree) && count($tree) > 0) {
            try {
                foreach ($tree as $element) {
                    if ($element['parent_id'] == $parentId && $element['id'] !== $element['parent_id']) {

                        $children = $this->buildTree($tree, $element['id']);

                        if ($children) {
                            $element[] = $children;
                        }
                        $branch[] = $element;
                    }
                }

            } catch (Exception $e) {
                echo $e->getTraceAsString();
            }

        } else {
            echo "\nYour array is empty, or you've tried to build the tree by using not an array.\n";
        }
        return $branch;
    }

    function outputTree($tree, $depth = 0)
    {
        if (is_array($tree) && count($tree) > 0) {
            if ($depth == 0) echo "<pre>" . PHP_EOL;

            foreach ($tree as $key => $val) {
                if (is_array($val)) {
                    for ($i = 0; $i < $depth / 2; $i++) {
                        if ($depth % 2 == 0) {
                            echo "-";
                        }
                    }
                    $this->outputTree($val, $depth + 1);
                } else if ($key == "name") {
                    echo $val . "\n";
                }
            }

            if ($depth == 0) echo "</pre>" . PHP_EOL;
        } else {
            echo "\nYour array is empty, or you've tried to output not an array.\n";
        }
    }

}

$obj = new TreeFromCSV();

$tree = $obj->readTree("tree.csv");
$tree = $obj->buildTree($tree);
$obj->outputTree($tree);




