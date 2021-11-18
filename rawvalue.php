<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_fields
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (!array_key_exists('field', $displayData))
{
	return;
}

$field = $displayData['field'];
$label = Text::_($field->label);
$value = $field->value;
$showLabel = $field->params->get('showlabel');
$prefix = Text::plural($field->params->get('prefix'), $value);
$suffix = Text::plural($field->params->get('suffix'), $value);
$labelClass = $field->params->get('label_render_class');
$renderClass = $field->params->get('render_class');

if ($value == '')
{
	return;
}

?>
<?php

// the block ABOVE comes from the original render.php file. The block BELOW is custom-made and replaces the rest of the original render.php file.

// Get the rawvalue and json_decode it
$rawvalue = $field->rawvalue;
$items = json_decode($rawvalue, true);

// this would display the value of the Custom Field taking into account the potential render class of the Custom Field
echo '<h2>Displaying the <strong>value</strong> of the Custom Field</h2>';
echo '<span class="field-value ' . $renderClass . '">' . $value . '</span>';

// this would display the rawvalue (json) of the Custom Field
echo '<h2>Displaying the <strong>rawvalue (json)</strong> of the Custom Field</h2>';
echo '<small><pre>'.print_r($items, true).'</pre></small>';

// this would display the rawvalue (json) as unordered list
echo '<h2>Displaying the <strong>rawvalue (json)</strong> as unordered list</h2>';
echo '<ul>';
foreach ($items as $item) {
    echo
      '<li>Slide<ul>'.
         '<li>' . ( $item['field1'] ?? '' ) . '</li>' .
         '<li>' . ( $item['field2'] ?? '' ) . '</li>' .
         '<li>' . ( $item['field3']['imagefile'] ?? '' ) . '</li>' .
      '</ul></li>';
}
echo '</ul>';

// this would display all what $displayData contains and that we could potentially use in an override / alternate layout
echo '<h2>Displaying the <strong>$displayData</strong> variable</h2>';
echo '<small><pre>'.print_r($displayData, true).'</pre></small>';
?>
