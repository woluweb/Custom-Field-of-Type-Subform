# Custom Field of Type Subform: Alternate Layouts

These Alternate Layouts come along with a series of articles published in the Joomla Community Magazine

For detailed instructions see the following two articles
- https://magazine.joomla.org/all-issues/october-2021/custom-fields-episode-7-part-1-one-custom-field-to-rule-them-all
- https://magazine.joomla.org/all-issues/november-2021/custom-fields-episode-7-part-2-one-custom-field-to-rule-them-all

The goal is to built a 100% native Carousel for Articles... based on 1 single Custom Field, namely the CF of Type Subform introduced with Joomla 4.

There are 5 Alternate Layouts because they were built step by step. Of course you only need to copy-paste 1 of them to your website to make it work.
See for youself which one will best suit your needs:
- step1-rawvalue.php
- step2-my-carousel-article-view-only.php
- step3-my-carousel-article-and-blog-views.php
- step4-my-carousel-article-and-blog-views-with-article-id.php, which requires also render.php
- step5-my-carousel.php

Last but not least, do not forget to adapt the ID of each Custom Field in the code. In our case:
- The Slide Title is a CF of Type Text having ID 1
- The Slide Duration is a CF of Type Integer having ID 2
- The Slide Image is a CF of Type Media having ID 3
- And finally the Carousel itself is a CF of Type Subform having ID 4
