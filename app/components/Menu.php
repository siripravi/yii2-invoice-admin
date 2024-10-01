<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Application menu.
 * 
 * @todo Emit events on insert/delete menu items.
 */
class Menu extends Component
{

    /**
     * @var array menu titles.
     */
    public $title = ['main' => 'Main menu'];
    
    /**
     * @var array menu items.
     */
    public $items = [];
    
    /**
     * @var boolean translate menu items by Yii::t().
     */
    public $translateItems = true;
    
    /**
     * Add item to menu.
     * @params string $menu
     * @params array $items likewise Menu::items property with special
     * additional property:
     * - guest: filter menu item by whether current user guest or not.
     * @return Menu
     */
    public function addItems($menu, array $items)
    {
        if (!isset($this->items[$menu])) {
            $this->items[$menu] = [];
        }
        $this->items[$menu] = array_merge($this->items[$menu], $items);
        return $this;
    }
    
    /**
     * Insert items to begin of menu.
     * @param string $menu
     * @param array $items
     * @return Menu
     * @see Menu::addItems()
     */
    public function insertItems($menu, array $items)
    {
        if (!isset($this->items[$menu])) {
            $this->items[$menu] = $items;
        } else {
            foreach ($items as $item) {
                array_unshift($this->items[$menu], $item);
            }
        }
        return $this;
    }
    
    /**
     * Get menu items.
     * @param string $menu
     * @param boolean $filter filter menu items or return as is.
     * @return Menu
     */
    public function getItems($menu, $filter = true)
    {
        $items = isset($this->items[$menu]) ? $this->items[$menu] : [];
        if ($items && $filter) {
            $items = $this->processMenuItems($items);
        }
        return $items;
    }
    
    /**
     * Get menu items for use in widget.
     * @param string $menu
     * @return array
     */
    public function getMenu($menu)
    {
        $items = $this->getItems($menu);
        if ($title = $this->getTitle($menu)) {
            array_unshift($items, ['label' => $title, 'options' => ['class' => 'header']]);
        }
        return $items;
    }
    
    /**
     * Process menu items.
     * @param array $items
     * @return array
     */
    protected function processMenuItems($items)
    {
        foreach ($items as $i => &$item) {
            // Filter menu items by user Guest attribute.
            if (isset($item['guest'])) {
                if (Yii::$app->user->isGuest && !$item['guest']) {
                    unset($items[$i]);
                    continue;
                }
                if (!Yii::$app->user->isGuest && $item['guest']) {
                    unset($items[$i]);
                    continue;
                }
            }
            // TODO: rename to 'permissions'.
            if (isset($item['roles'])) {
                $access = false;
                if (is_callable($item['roles'])) {
                    $permissions = call_user_func($item['roles']);
                } else {
                    $permissions = $item['roles'];
                }
                foreach ($permissions as $permName) {
                    if (Yii::$app->user->can($permName)) {
                        $access = true;
                        break;
                    }
                }
                if (!$access) {
                    unset($items[$i]);
                    continue;
                }
            }
            if (isset($item['visible'])) {
                if ((is_callable($item['visible']) && !call_user_func($item['visible']))
                        || !$item['visible']) {
                    unset($items[$i]);
                    continue;
                }
            }
            if ($this->translateItems) {
                $item['label'] = Yii::t('app', $item['label']);
            }
            if (isset($item['items'])) {
                $item['items'] = $this->processMenuItems($item['items']);
            }
        }
        return $items;
    }
    
    /**
     * @param string $menu
     * @return Menu
     */
    public function clearItems($menu)
    {
        $this->items[$menu] = [];
        return $this;
    }
    
    /**
     * Set menu title.
     * @param string $menu
     * @param string $title
     * @return Menu
     */
    public function setTitle($menu, $title)
    {
        $this->title[$menu] = $title;
        return $this;
    }
    
    /**
     * @param string $menu
     * @return string
     */
    public function getTitle($menu)
    {
        return isset($this->title[$menu]) ? $this->title[$menu] : '';
    }
    
    /**
     * Insert menu items before specified item.
     * @param string $menu menu name
     * @param string $label item label before items will be inserted. To
     * insert items into submenu use '/' to separate labels. For example,
     * to insert items into Account submenu use label 'User/Account'.
     * @param array $items
     * @return Menu
     */
    public function insertBefore($menu, $label, array $items)
    {
        $this->items[$menu] = $this->processInsert($this->getItems($menu, false), $items, $label);
        return $this;
    }
    
    /**
     * @see Menu::insertBefore()
     */
    public function insertAfter($menu, $label, array $items)
    {
        $this->items[$menu] = $this->processInsert($this->getItems($menu, false), $items, $label, true);
        return $this;
    }
    
    /**
     * Helper function for items insert operation.
     * @param array $items source menu items
     * @param array $insertItems menu items to be inserted
     * @param string $label anchor item label
     * @param boolean $after insert items after anchor label
     * @param string $level internal. Current menu level
     * @return array
     */
    protected function processInsert($items, $insertItems, $label, $after = false, $level = '')
    {
        foreach ($items as $i => $item) {
            $lvl = $level . $item['label'];
            if ($lvl === $label) {
                if ($after) {
                    if ($i === count($items) - 1) {
                        $items = array_merge($items, $insertItems);
                    } else {
                        array_splice($items, $i + 1, 0, $insertItems);
                    }
                } else {
                    array_splice($items, $i, 0, $insertItems);
                }
                break;
            }
            if (isset($item['items'])) {
                $items[$i]['items'] = $this->processInsert($item['items'], $insertItems, $label, $after, $level . $item['label'] . '/');
            }
        }
        return $items;
    }
    
}
