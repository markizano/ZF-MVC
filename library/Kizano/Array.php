<?php
/**
 * Kizano/Array.php
 *
 * PHP version 5.*
 *
 *   Namespace placeholder for functions that would normally be free-floating.
 *   Copyright (C) 2010 Markizano Draconus <markizano@markizano.net>
 *
 *   This class is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Kizano
 * @package   Kizano
 * @author    Markizano Draconus <markizano@markizano.net>
 * @copyright 2010 Markizano Draconus <markizano@markizano.net>
 * @license   http://www.gnu.org/licenses/gpl.html GNU Public License
 */

/**
 *  As surprising as it may be, PHP does not support these functions natively. I've searched the
 *  documentation and the source, but to no avail have I found a native solution to these functions
 *  and their needs, therefore they have been added here to provide additional functionality to
 *  PHP's native functionality.
 */
class Kizano_Array
{

    /**
     *  Behaves similarly to array_merge and + in that it will merge two or more arrays, except it checks
     *  for existing keys and attempts to overwrite them instead of appending them. Also, working with
     *  array_merge and numeric keys provided unexpected and undesired results. The primary reason
     *  this function was created to provide a more stable way of merging two or more arrays and make
     *  their values overwrite one another instead of appending as with array_merge().
     *  The advantage to using this function over the + operator is that the values of the array will
     *  be preserved as well.
     *  http://php.net/manual/en/function.array-merge.php
     *  @Example:
     *      <?php
     *      
     *      $defaults = array(
     *        array(
     *          'view' => false,
     *          'edit' => false,
     *        ),
     *        array(
     *          'view' => false,
     *          'edit' => false,
     *        ),
     *        array(
     *          'view' => false,
     *          'edit' => false,
     *        ),
     *        array(
     *          'view' => false,
     *          'edit' => false,
     *        ),
     *      );
     *      
     *      $user = array(
     *        2 => array(
     *          'view' => true,
     *        ),
     *        3 => array(
     *          'edit' => true,
     *        )
     *      );
     *      
     *      var_dump(Kizano_Array::merge($defaults, $user));
     *      var_dump(array_merge($defaults, $user));
     *      var_dump($user + $defaults);
     *      ?>
     *      
     *      array
     *        0 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        1 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        2 => 
     *          array
     *            'view' => boolean true
     *            'edit' => boolean false
     *        3 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean true
     *      
     *      array
     *        0 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        1 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        2 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        3 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        4 => 
     *          array
     *            'view' => boolean true
     *        5 => 
     *          array
     *            'edit' => boolean true
     *      
     *      array
     *        2 => 
     *          array
     *            'view' => boolean true
     *        3 => 
     *          array
     *            'edit' => boolean true
     *        0 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *        1 => 
     *          array
     *            'view' => boolean false
     *            'edit' => boolean false
     *  
     *  @notes  As you can see in the code example above, array_merge will first re-order the keys
     *      of the provided arrays, and then attempt to merge them if they are numeric; however,
     *      we need the keys to be overwritten without special treatment instead of re-ordered in
     *      any fashion.
     *      In addition, the + operator does not generate the desired results because of the
     *      following points:
     *          - It does not return the array in any ordered fashion.
     *          - It overwrites the target result with the array added to the defaults entirely.
     *          - It does not preserve the original values in the default array.
     *      The array_merge() function does not return the desired results because of the following
     *      reasons:
     *          - It re-orders the keys of the array if the provided arrays are numeric.
     *          - If using numeric keys, the values of the arrays are appended instead of overwritten.
     *          - It overwrites the default value with the target value.
     *
     *      This function was so heavily documented because of a lot of discussion over its purpose.
     *  
     *  @param-list     arrays to merge
     *  @return array
     */
    public static function merge()
    {
        $result = array();
        # For each of the arguments passed into this function
        foreach (func_get_args() as $arrays) {
            if (!is_array($arrays) || empty($arrays)) {
                continue;
            }

            # Process each argument as if it were an array
            foreach ($arrays as $a => $array) {
                if (is_array($array)) {
                    if (isset($result[$a]) && is_array($result[$a])) {
                        $result[$a] = self::merge($result[$a], $array);
                    } else {
                       $result[$a] = self::merge($array);
                    }
                } else {
                    $result[$a] = $array;
                }
            }
        }

        return $result;
    }

    /**
     *  Cuts an array in half and returns the halves as an array.
     *  @param array            Array   The array to split.
     *  @param preserve_keys    Boolean Whether to preserve the keys in the original array.
     *  @return array
     */
    public static function half($array, $preserve_keys = false)
    {
        $half = count($array) / 2;
        return array(
            array_slice($array, 0, $half, $preserve_keys),
            array_slice($array, $half, null, $preserve_keys)
        );
    }
}

