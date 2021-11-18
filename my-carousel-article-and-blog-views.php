<?php

defined('_JEXEC') or die;

if (!array_key_exists('field', $displayData))
{
	return;
}

$field = $displayData['field'];
// Get the rawvalue
$rawvalue = $field->rawvalue ?? '';

// If we display the Carousel (being our Custom Field of Type Subform) on a blog view we will get several Carousels on the same page.
// Still by definition each Carousel container should have a unique ID.
// So the question is: how to get a unique ID for each Carousel container, knowing that the Custom Field as such is totally agnostic of the Context (with other words, we don't have access to the article ID)?
// Note: a container ID should not start with a digit

// SOLUTION 1 (requiring only the current file): we create simply an MD5 hash based on the whole $rawvalue
// So even 2 articles having *almost* the same carousel (same images for example but a little difference in the text or in the number of images) will have a different hash
// This is a pragmatic solution which should cover more than 99% of the cases
$carouselContainerId = 'carousel-field'. $field->id . '-'. md5($rawvalue, false); 

// SOLUTION 2 (requiring more than the current file): we find a solution to get the Article ID!
// This will be done in a later stage, but here is already the explanation:
// For this create an override of components/com_fields/layouts/fields/render.php in /templates/[template]/html/layouts/com_fields/fields
// In that override we will change
// $content = FieldsHelper::render($context, 'field.' . $layout, array('field' => $field));
// into
// $content = FieldsHelper::render($context, 'field.' . $layout, array('itemid' => $item->id, 'field' => $field));
// and then we can define $carouselContainerId as follows
// $carouselContainerId = 'carousel-field'. $field->id . '-'. $displayData['itemid'];

if ($rawvalue == '')
{
	return;
}

?>

<?php
use Joomla\CMS\Factory; // necessary bc we use herafter Factory::getApplication() and Factory::getDocument()
$app = Factory::getApplication(); // JFactory is indeed deprecated in J!4
$view = $app->input->getCMD('view', ''); // "view" would output "article" or "category" for example
// $id = $app->input->getCMD('id', ''); // "id" gives the id of the category if blog view, the id of the article if article view. So not useful here
if ('article' !== $view) {
//    return; // comment this line if you want the carousel to also display on the blog view for example
}
?>

<?php // https://docs.joomla.org/J4.x:Using_Bootstrap_Components_in_Joomla_4
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.carousel', '#' . $carouselContainerId, ['interval' => 3000, 'pause' => 'false']); // selector is necessary for the potentials Options to work ?>

<?php $items = json_decode($rawvalue, true); ?>

<div id="<?php echo $carouselContainerId; ?>" class="carousel slide carousel-fade" data-bs-ride="carousel">
	<div class="carousel-indicators">
		<?php $first=true; $i=0; ?>
			<?php foreach($items as $item): ?>
				<button <?php if ($first) {echo "class=\"active\""; $first=false;} ?> type="button" data-bs-target="#<?php echo $carouselContainerId; ?>" data-bs-slide-to="<?php echo $i; ?>" aria-current="true" aria-label="<?php echo "Slide " . $i+1; ?>"></button>
			<?php $i=$i+1; ?>
		<?php endforeach; ?>
	</div>
	<div class="carousel-inner">
		<?php $first=true; ?>
		<?php foreach($items as $item): ?>
			<?php
				// Note: field1 in $item['field1'] refers to the Custom Field having ID 1 - nothing to do with the Order of fields within the Custom Field of Type SubForm
				// If necessary adapt the next three lines according to the ID of the Custom Fields on *your* site
				$slideTitle = $item['field1'] ?? '';
				$slideDuration = $item['field2'] ?? '1';
				$slideImage = $item['field3']['imagefile'] ?? '';
			?>
			<div class="carousel-item <?php if ($first) {echo "active"; $first=false;} ?>" data-bs-interval="<?php echo 1000*$slideDuration; ?>">
				<img class="d-block w-100" src="<?php echo $slideImage; ?>" />
				<div class="carousel-caption d-none d-md-block">
					<h5><?php echo $slideTitle; ?></h5>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselContainerId; ?>" data-bs-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="visually-hidden">Previous</span>
	</button>
	<button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselContainerId; ?>" data-bs-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="visually-hidden">Next</span>
	</button>
</div>

<?php
$carouselCSS = <<<MYCSS
/* example of CSS for bootstrap.carousel - adding a background to the carousel-caption - see https://ui.glass/generator/ for glassmorphism CSS */
.carousel-caption {
bottom: 40%; /* otherwise with our background too close from Indicators with the default 1.25rem */
left: 25%; /* to make it narrower than with the default 15% */
right: 25%; /* to make it narrower than with the default 15% */
backdrop-filter: blur(16px) saturate(180%);
-webkit-backdrop-filter: blur(16px) saturate(180%);
background-color: rgba(0,0,0,0.5);
border-radius: 12px;
border: 1px solid rgba(255, 255, 255, 0.125);
}
/* by default the custom fields appear in a Unordered List. To make the carousel start on the left and hide the bullet point we use a negative margin as a first approach */
li.field-entry.mycarousel {list-style-type: none; margin-left: -2rem}
MYCSS;

// use Joomla\CMS\Factory; // is already called above so commented here bc can only be put once in the file
$doc = Factory::getDocument();
$doc->addStyleDeclaration($carouselCSS); // CSS will be injected only once even if this layout is called multiple times on a page

// Ununeeded stuff, just needed during testing/development. Uncomment the 'return' to execute the code hereafter
return;

echo 'test';
?>
