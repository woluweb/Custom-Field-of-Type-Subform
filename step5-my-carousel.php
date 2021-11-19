<?php

/**
 * This is an Alternate Layout for Custom Fields, namely of /components/com_fields/layouts/field/render.php.
 * For an explanation of Alternate Layout for Custom Fields see for example
 * https://magazine.joomla.org/all-issues/may-2021/explore-the-core-play-with-custom-fields-to-enrich-your-content-or-your-design
 *
 * Joomla 4 has introduced a new Type of Custom Fields: SubForm
 * This Alternate Layout shows an example of what can be done with such a CF, creating a carousel for an Article
 * The code herafter might use PHP7.x syntax since J!4 requires PHP 7.2+ anyway
 */

defined('_JEXEC') || die;

//region 0. return earlier if nothing to do
// No data? Stop
if (!isset($displayData)) {
    return;
}

// Explanation of the '??' syntax: fetches the value of $displayData['field'] and returns ''
// if it does not exist - shorter syntax than playing with isset()
// See https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.null-coalesce-op
$field = $displayData['field'] ?? '';

// No field? Stop
if ('' === $field) { // "Yoda Style", avoids typo like $field='' instead of $field==''
    return;
}

$value = $field->value ?? '';

// No value? Stop
if ('' === $value) {
    return;
}

// Get Joomla objects and initialize variables
$app = \Joomla\CMS\Factory::getApplication(); // JFactory is indeed deprecated in J!4

// 'view' would output 'article' or 'category' for example
$view = $app->input->getCmd('view', '');

// Note: the following line gives the id of the category if blog view, the id of the article if article view
// $id = $app->input->getCmd('id', '');
// So we cannot use it to create a unique #id to the container of each article in a blog view

// if the view is not an article view (but a blog view or whatever)
// then nothing will be displayed. Stop
if ('article' !== $view) {
//    return; // comment this line if you want the carousel to also display on the blog view for example
}

//endregion

//region 1. Prepare variables based on params
// added for Alternated Layout

// Get the rawvalue
$rawvalue = $field->rawvalue ?? '';

// giving a unique ID to the carousel container. NB: the ID cannot start with a digit
$carouselContainerId = 'carousel-field' . $field->id . '-' . md5($rawvalue, false);

// Make sure to throw an exception if the JSON content is wrong (bad syntax)
$items = json_decode($rawvalue, true, 512, JSON_THROW_ON_ERROR);

// Get parameters
$showLabel   = $field->params->get('showlabel') ?? true;
$labelClass  = $field->params->get('label_render_class') ?? '';
$renderClass = $field->params->get('render_class') ?? '';

//endregion

// https://docs.joomla.org/J4.x:Using_Bootstrap_Components_in_Joomla_4

//region 2. Create the buttons         - Set the carouselButtons variable
$firstSlide      = true;
$slideNumber     = 0;
$carouselButtons = '';

$templateCarouselButtons =
    '<button class="%s" type="button" ' .
        'data-bs-target="#%s" ' .
        'data-bs-slide-to="%s" ' .
        'aria-current="true" aria-label="%s" />';

foreach ($items as $item) {
    $carouselButtons .= sprintf(
        $templateCarouselButtons,
        ($firstSlide ? 'active' : ''),         // Active or not
        $carouselContainerId,                  // Carousel DIV ID
        strval($slideNumber),                  // The slide-to number - starts with 0
        strval('Slide ' . ($slideNumber + 1))  // The aria-label for the slide - starts with 1
    );

    $firstSlide = false;
    ++$slideNumber;
}

//endregion

//region 3. Create the different items - Set the carouselItems variable
$firstSlide    = true;
$carouselItems = '';

$templateCarouselItems =
    '<div class="carousel-item %s" data-bs-interval="%d">' .
        '<img class="d-block w-100" src="%s" />' .
        '<div class="carousel-caption d-none d-md-block">' .
            '<h5>%s</h5>' .
        '</div>' .
    '</div>';

foreach ($items as $item) {
    // Note: field1 in $item['field1'] refers to the Custom Field having ID 1
    // nothing to do with the Order of fields within the Custom Field of Type SubForm
    // If necessary adapt the next three lines according to the ID of the Custom Fields on *your* site
    $slideTitle    = $item['field1'] ?? '';
    $slideDuration = (int) $item['field2'] ?? 1;
    $slideImage    = $item['field3']['imagefile'] ?? '';

    $carouselItems .= sprintf(
        $templateCarouselItems,
        ($firstSlide ? 'active' : ''),      // Active or not
        (1000 * $slideDuration),            // The interval
        $slideImage,                        // The image
        $slideTitle                         // The title (heading 5)
    );

    $firstSlide = false;
}

//endregion

//region 4. Put things together        - Set the final carouselDiv variable
$templateCarouselDiv =
    '<div id="%s" ' .
        'class="carousel slide carousel-fade" data-bs-ride="carousel">' .
        '<div class="carousel-indicators">%s</div>' .
        '<div class="carousel-inner">%s</div>' .
        '<button class="carousel-control-prev" type="button" ' .
            'data-bs-target="#%s" data-bs-slide="prev"> ' .
            '<span class="carousel-control-prev-icon" aria-hidden="true"></span>' .
            '<span class="visually-hidden">Previous</span>' .
        '</button>' .
        '<button class="carousel-control-next" type="button" ' .
            'data-bs-target="#%s" data-bs-slide="next">' .
            '<span class="carousel-control-next-icon" aria-hidden="true"></span>' .
            '<span class="visually-hidden">Next</span>' .
        '</button>' .
    '</div>';

$carouselDiv = sprintf(
    $templateCarouselDiv,
    $carouselContainerId, // carousel ID for the DIV and the links
    $carouselButtons,       // carousel-indicators
    $carouselItems,          // carousel-inner
    $carouselContainerId, // carousel ID for the DIV and the links
    $carouselContainerId // carousel ID for the DIV and the links
);
//endregion

//region 5. Define our CSS             - Set the carouselCSS variable
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
/* by default the custom fields appear in a Unordered List. To make the carousel start on the left and hide the bullet point we use a negative margin */
li.field-entry.mycarousel {list-style-type: none; margin-left: -2rem}
MYCSS;
//endregion

//region 6. Joomla part - Inject the carousel in the page
// selector is necessary for the potentials Options to work
\Joomla\CMS\HTML\HTMLHelper::_(
    'bootstrap.carousel ',
    $carouselContainerId,
    [
        'interval' => 3000,
        'pause'    => 'false'
    ]
);

// And inject the CSS and the HTML of our carousel
$doc = \Joomla\CMS\Factory::getDocument();

// CSS will be injected only once even if this layout is called multiple times on a page
$doc->addStyleDeclaration($carouselCSS);

echo $carouselDiv;

//endregion

//region 7. Ununeeded stuff, just needed during testing/development. Uncomment the 'return' to execute the code hereafter
return;

echo 'test';
//endregion
