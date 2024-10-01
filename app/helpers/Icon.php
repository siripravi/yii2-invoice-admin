<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\helpers;

/**
 * Icon
 *
 * @author skoro
 */
class Icon
{

    /**
     * Well known Bootstrap icons.
     */
    const OK = '<i class="glyphicon glyphicon-ok"></i>';
    const REMOVE = '<i class="glyphicon glyphicon-remove"></i>';
    const USER = '<i class="glyphicon glyphicon-user"></i>';
    const STAR = '<i class="glyphicon glyphicon-star"></i>';
    const PLUS = '<i class="glyphicon glyphicon-plus"></i>';
    const MINUS = '<i class="glyphicon glyphicon-minus"></i>';
    const TRASH = '<i class="glyphicon glyphicon-trash"></i>';
    const COG = '<i class="glyphicon glyphicon-cog"></i>';
    const EYE_OPEN = '<i class="glyphicon glyphicon-eye-open"></i>';
    const PAPERCLIP = '<i class="glyphicon glyphicon-paperclip"></i>';
    const TASKS = '<i class="glyphicon glyphicon-tasks"></i>';
    const FILTER = '<i class="glyphicon glyphicon-filter"></i>';
    const DASHBOARD = '<i class="glyphicon glyphicon-dashboard"></i>';
    const SORT = '<i class="glyphicon glyphicon-sort"></i>';
    const EDIT = '<i class="glyphicon glyphicon-edit"></i>';
    const CHECK = '<i class="glyphicon glyphicon-check"></i>';
    const UNCHECKED = '<i class="glyphicon glyphicon-unchecked"></i>';
    const STATS = '<i class="glyphicon glyphicon-stats"></i>';
    const FLASH = '<i class="glyphicon glyphicon-flash"></i>';
    const SEARCH = '<i class="glyphicon glyphicon-search"></i>';
    const STAR_EMPTY = '<i class="glyphicon glyphicon-star-empty"></i>';
    const ENVELOPE = '<i class="glyphicon glyphicon-envelope"></i>';
    
    /**
     * Returns icon html and optional (unencoded) text after icon.
     * @param string $ico icon class (for example, 'fa fa-circle').
     * @param string $text optional text after icon.
     * @return string
     */
    public static function icon($ico, $text = '')
    {
        return '<i class="' . $ico . '"></i> ' . $text;
    }
    
}
