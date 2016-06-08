<?php
namespace Acciona\Users\Utils;

use Traversable;

class CollectionsUtils
{
  public static function flatMap(callable $callback, $collection)
  {
    $flattened = [];
    foreach ($collection as $element) {
      $result = $callback($element);
      if (is_array($result) || $result instanceof Traversable) {
          foreach ($result as $item) {
              $flattened[] = $item;
          }
      } elseif ($result !== null) {
          $flattened[] = $result;
      }
    }
    return $flattened;
  }
}
