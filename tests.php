<?php

# 5 1 2 + 4 * + 3 -                 ... 14
# 8 3 * 2 /     ... 12

// (3*8)/2


class Stack
{
    public function __construct(public array $array = [])
    {
    }

    public function push($value): void
    {
        array_unshift($this->array, $value);
    }

    public function getArray(): array {
        return $this->array;
    }

    public function pop(): mixed
    {
        return array_shift($this->array);
    }
}

$inputString = "8 3 * 2 /";
$inputArray = explode(" ", $inputString);
$stack = new Stack($inputArray);
while(!empty($arr = $stack->getArray())) {
    foreach ($arr as $item) {
        if(!is_numeric($item)) {
            $first = (int)$stack->pop();
            $second = (int)$stack->pop();
            $result = $first + $second;
            $stack->push($item);
        }
    }
}