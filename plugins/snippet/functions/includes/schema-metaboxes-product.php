<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-PRODUCT.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of Product
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Array of values
// =============================================================================

// Array of values
// =============================================================================

return array (
  array (
    'id' => '_snippet_product_type',
    'name' => '@type',
    'label' => __( 'Type', '__x__' ),
    'description' => __( 'Type of schema', '__x__' ),
    'schema_type' => '',
    'type' => 'type',
  ),
  array (
    'id' => '_snippet_product_name',
    'name' => 'name',
    'label' => __( 'Name', '__x__' ),
    'description' => __( 'The name of the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_product_brand',
    'name' => 'brand',
    'label' => __( 'Brand', '__x__' ),
    'description' => __( 'The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.', '__x__' ),
    'schema_type' => 'Brand',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_product_image',
    'name' => 'image',
    'label' => __( 'Image', '__x__' ),
    'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'media',
  ),

  array (
    'id' => '_snippet_product_offers',
    'name' => 'offers',
    'label' => __( 'Offer', '__x__' ),
    'description' => __( 'An offer to provide this item&#x2014;for example, an offer to sell a product, rent the DVD of a movie, perform a service, or give away tickets to an event.', '__x__' ),
    'schema_type' => 'Offer',
    'type' => 'offer',
  ),


  // //
  // // These fields are an OFFER Schema
  // //
  // array (
  //   'id' => '_snippet_product_offer_price',
  //   'name' => 'price',
  //   'label' => __( 'Price', '__x__' ),
  //   'description' => __( 'The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes. Usage guidelines:  Use the priceCurrency property (with ISO 4217 codes e.g. "USD") instead of including ambiguous symbols such as "$" in the value. Use "." (Unicode "FULL STOP" (U+002E)) rather than "," to indicate a decimal point. Avoid using these symbols as a readability separator. Note that both RDFa and Microdata syntax allow the use of a "content=" attribute for publishing simple machine-readable values alongside more human-friendly formatting. Use values from 0123456789 (Unicode "DIGIT ZERO" (U+0030) to "DIGIT NINE" (U+0039)) rather than superficially similiar Unicode symbols.  ', '__x__' ),
  //   'schema_type' => 'Number',
  //   'type' => 'money',
  //   'schema_parent' => 'Offer',
  // ),
  // array (
  //   'id' => '_snippet_product_offer_price_currency',
  //   'name' => 'priceCurrency',
  //   'label' => __( 'Price Currency', '__x__' ),
  //   'description' => __( 'The currency (in 3-letter ISO 4217 format) of the price or a price component, when attached to PriceSpecification and its subtypes.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'currency',
  //   'schema_parent' => 'Offer',
  // ),
  // array (
  //   'id' => '_snippet_product_offer_url',
  //   'name' => 'url',
  //   'label' => __( 'Url', '__x__' ),
  //   'description' => __( "URL to buy check this offer (not the product's URL).", '__x__' ),
  //   'schema_type' => 'URL',
  //   'type' => 'text',
  //   'schema_parent' => 'Offer',
  // ),
  // array (
  //   'id' => '_snippet_product_offer_item_condition',
  //   'name' => 'itemCondition',
  //   'label' => __( 'Item Condition', '__x__' ),
  //   'description' => __( 'A predefined value from OfferItemCondition or a textual description of the condition of the product or service, or the products or services included in the offer.', '__x__' ),
  //   'schema_type' => 'OfferItemCondition',
  //   'type' => 'item-condition',
  //   'schema_parent' => 'Offer',
  // ),
  // array (
  //   'id' => '_snippet_product_offer_availability',
  //   'name' => 'availability',
  //   'label' => __( 'Availability', '__x__' ),
  //   'description' => __( 'The availability of this item&#x2014;for example In stock, Out of stock, Pre-order, etc.', '__x__' ),
  //   'schema_type' => 'ItemAvailability',
  //   'type' => 'availability',
  //   'schema_parent' => 'Offer',
  // ),





  // 1 =>
  // array (
  //   'id' => '_snippet_product_additional_property',
  //   'name' => 'additionalProperty',
  //   'label' => __( 'Additional Property', '__x__' ),
  //   'description' => __( 'A property-value pair representing an additional characteristics of the entitity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.', '__x__' ),
  //   'schema_type' => 'PropertyValue',
  //   'type' => 'text',
  // ),
  // 2 =>
  // array (
  //   'id' => '_snippet_product_aggregate_rating',
  //   'name' => 'aggregateRating',
  //   'label' => __( 'Aggregate Rating', '__x__' ),
  //   'description' => __( 'The overall rating, based on a collection of reviews or ratings, of the item.', '__x__' ),
  //   'schema_type' => 'AggregateRating',
  //   'type' => 'text',
  // ),
  // 3 =>
  // array (
  //   'id' => '_snippet_product_audience',
  //   'name' => 'audience',
  //   'label' => __( 'Audience', '__x__' ),
  //   'description' => __( 'An intended audience, i.e. a group for whom something was created. Supersedes serviceAudience.', '__x__' ),
  //   'schema_type' => 'Audience',
  //   'type' => 'text',
  // ),
  // 4 =>
  // array (
  //   'id' => '_snippet_product_award',
  //   'name' => 'award',
  //   'label' => __( 'Award', '__x__' ),
  //   'description' => __( 'An award won by or for this item. Supersedes awards.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 5 =>
  // array (
  //   'id' => '_snippet_product_brand',
  //   'name' => 'brand',
  //   'label' => __( 'Brand', '__x__' ),
  //   'description' => __( 'The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.', '__x__' ),
  //   'schema_type' => 'Brand',
  //   'type' => 'text',
  // ),
  // 6 =>
  // array (
  //   'id' => '_snippet_product_category',
  //   'name' => 'category',
  //   'label' => __( 'Category', '__x__' ),
  //   'description' => __( 'A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 7 =>
  // array (
  //   'id' => '_snippet_product_color',
  //   'name' => 'color',
  //   'label' => __( 'Color', '__x__' ),
  //   'description' => __( 'The color of the product.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 8 =>
  // array (
  //   'id' => '_snippet_product_depth',
  //   'name' => 'depth',
  //   'label' => __( 'Depth', '__x__' ),
  //   'description' => __( 'The depth of the item.', '__x__' ),
  //   'schema_type' => 'Distance',
  //   'type' => 'text',
  // ),
  // 9 =>
  // array (
  //   'id' => '_snippet_product_gtin12',
  //   'name' => 'gtin12',
  //   'label' => __( 'Gtin12', '__x__' ),
  //   'description' => __( 'The GTIN-12 code of the product, or the product to which the offer refers. The GTIN-12 is the 12-digit GS1 Identification Key composed of a U.P.C. Company Prefix, Item Reference, and Check Digit used to identify trade items. See GS1 GTIN Summary for more details.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 10 =>
  // array (
  //   'id' => '_snippet_product_gtin13',
  //   'name' => 'gtin13',
  //   'label' => __( 'Gtin13', '__x__' ),
  //   'description' => __( 'The GTIN-13 code of the product, or the product to which the offer refers. This is equivalent to 13-digit ISBN codes and EAN UCC-13. Former 12-digit UPC codes can be converted into a GTIN-13 code by simply adding a preceeding zero. See GS1 GTIN Summary for more details.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 11 =>
  // array (
  //   'id' => '_snippet_product_gtin14',
  //   'name' => 'gtin14',
  //   'label' => __( 'Gtin14', '__x__' ),
  //   'description' => __( 'The GTIN-14 code of the product, or the product to which the offer refers. See GS1 GTIN Summary for more details.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 12 =>
  // array (
  //   'id' => '_snippet_product_gtin8',
  //   'name' => 'gtin8',
  //   'label' => __( 'Gtin8', '__x__' ),
  //   'description' => __( 'The GTIN-8 code of the product, or the product to which the offer refers. This code is also known as EAN/UCC-8 or 8-digit EAN. See GS1 GTIN Summary for more details.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 13 =>
  // array (
  //   'id' => '_snippet_product_height',
  //   'name' => 'height',
  //   'label' => __( 'Height', '__x__' ),
  //   'description' => __( 'The height of the item.', '__x__' ),
  //   'schema_type' => 'Distance',
  //   'type' => 'text',
  // ),
  // 14 =>
  // array (
  //   'id' => '_snippet_product_is_accessory_or_spare_part_for',
  //   'name' => 'isAccessoryOrSparePartFor',
  //   'label' => __( 'Is Accessory Or Spare Part For', '__x__' ),
  //   'description' => __( 'A pointer to another product (or multiple products) for which this product is an accessory or spare part.', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 15 =>
  // array (
  //   'id' => '_snippet_product_is_consumable_for',
  //   'name' => 'isConsumableFor',
  //   'label' => __( 'Is Consumable For', '__x__' ),
  //   'description' => __( 'A pointer to another product (or multiple products) for which this product is a consumable.', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 16 =>
  // array (
  //   'id' => '_snippet_product_is_related_to',
  //   'name' => 'isRelatedTo',
  //   'label' => __( 'Is Related To', '__x__' ),
  //   'description' => __( 'A pointer to another, somehow related product (or multiple products).', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 17 =>
  // array (
  //   'id' => '_snippet_product_is_similar_to',
  //   'name' => 'isSimilarTo',
  //   'label' => __( 'Is Similar To', '__x__' ),
  //   'description' => __( 'A pointer to another, functionally similar product (or multiple products).', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 18 =>
  // array (
  //   'id' => '_snippet_product_item_condition',
  //   'name' => 'itemCondition',
  //   'label' => __( 'Item Condition', '__x__' ),
  //   'description' => __( 'A predefined value from OfferItemCondition or a textual description of the condition of the product or service, or the products or services included in the offer.', '__x__' ),
  //   'schema_type' => 'OfferItemCondition',
  //   'type' => 'text',
  // ),
  // 19 =>
  // array (
  //   'id' => '_snippet_product_logo',
  //   'name' => 'logo',
  //   'label' => __( 'Logo', '__x__' ),
  //   'description' => __( 'An associated logo.', '__x__' ),
  //   'schema_type' => 'ImageObject',
  //   'type' => 'text',
  // ),
  // 20 =>
  // array (
  //   'id' => '_snippet_product_manufacturer',
  //   'name' => 'manufacturer',
  //   'label' => __( 'Manufacturer', '__x__' ),
  //   'description' => __( 'The manufacturer of the product.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 21 =>
  // array (
  //   'id' => '_snippet_product_model',
  //   'name' => 'model',
  //   'label' => __( 'Model', '__x__' ),
  //   'description' => __( 'The model of the product. Use with the URL of a ProductModel or a textual representation of the model identifier. The URL of the ProductModel can be from an external source. It is recommended to additionally provide strong product identifiers via the gtin8/gtin13/gtin14 and mpn properties.', '__x__' ),
  //   'schema_type' => 'ProductModel',
  //   'type' => 'text',
  // ),
  // 22 =>
  // array (
  //   'id' => '_snippet_product_mpn',
  //   'name' => 'mpn',
  //   'label' => __( 'Mpn', '__x__' ),
  //   'description' => __( 'The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 23 =>
  // array (
  //   'id' => '_snippet_product_offers',
  //   'name' => 'offers',
  //   'label' => __( 'Offers', '__x__' ),
  //   'description' => __( 'An offer to provide this item&#x2014;for example, an offer to sell a product, rent the DVD of a movie, perform a service, or give away tickets to an event.', '__x__' ),
  //   'schema_type' => 'Offer',
  //   'type' => 'text',
  // ),
  // 24 =>
  // array (
  //   'id' => '_snippet_product_product_id',
  //   'name' => 'productID',
  //   'label' => __( 'Product ID', '__x__' ),
  //   'description' => __( 'The product identifier, such as ISBN. For example: meta itemprop="productID" content="isbn:123-456-789".', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 25 =>
  // array (
  //   'id' => '_snippet_product_production_date',
  //   'name' => 'productionDate',
  //   'label' => __( 'Production Date', '__x__' ),
  //   'description' => __( 'The date of production of the item, e.g. vehicle.', '__x__' ),
  //   'schema_type' => 'Date',
  //   'type' => 'text',
  // ),
  // 26 =>
  // array (
  //   'id' => '_snippet_product_purchase_date',
  //   'name' => 'purchaseDate',
  //   'label' => __( 'Purchase Date', '__x__' ),
  //   'description' => __( 'The date the item e.g. vehicle was purchased by the current owner.', '__x__' ),
  //   'schema_type' => 'Date',
  //   'type' => 'text',
  // ),
  // 27 =>
  // array (
  //   'id' => '_snippet_product_release_date',
  //   'name' => 'releaseDate',
  //   'label' => __( 'Release Date', '__x__' ),
  //   'description' => __( 'The release date of a product or product model. This can be used to distinguish the exact variant of a product.', '__x__' ),
  //   'schema_type' => 'Date',
  //   'type' => 'text',
  // ),
  // 28 =>
  // array (
  //   'id' => '_snippet_product_review',
  //   'name' => 'review',
  //   'label' => __( 'Review', '__x__' ),
  //   'description' => __( 'A review of the item. Supersedes reviews.', '__x__' ),
  //   'schema_type' => 'Review',
  //   'type' => 'text',
  // ),
  // 29 =>
  // array (
  //   'id' => '_snippet_product_sku',
  //   'name' => 'sku',
  //   'label' => __( 'Sku', '__x__' ),
  //   'description' => __( 'The Stock Keeping Unit (SKU), i.e. a merchant-specific identifier for a product or service, or the product to which the offer refers.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 30 =>
  // array (
  //   'id' => '_snippet_product_weight',
  //   'name' => 'weight',
  //   'label' => __( 'Weight', '__x__' ),
  //   'description' => __( 'The weight of the product or person.', '__x__' ),
  //   'schema_type' => 'QuantitativeValue',
  //   'type' => 'text',
  // ),
  // 31 =>
  // array (
  //   'id' => '_snippet_product_width',
  //   'name' => 'width',
  //   'label' => __( 'Width', '__x__' ),
  //   'description' => __( 'The width of the item.', '__x__' ),
  //   'schema_type' => 'Distance',
  //   'type' => 'text',
  // ),
  // 32 =>
  // array (
  //   'id' => '_snippet_product_description',
  //   'name' => 'description',
  //   'label' => __( 'Description', '__x__' ),
  //   'description' => __( 'A description of the item.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 33 =>
  // array (
  //   'id' => '_snippet_product_disambiguating_description',
  //   'name' => 'disambiguatingDescription',
  //   'label' => __( 'Disambiguating Description', '__x__' ),
  //   'description' => __( 'A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 34 =>
  // array (
  //   'id' => '_snippet_product_image',
  //   'name' => 'image',
  //   'label' => __( 'Image', '__x__' ),
  //   'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
  //   'schema_type' => 'ImageObject',
  //   'type' => 'text',
  // ),
  // 35 =>
  // array (
  //   'id' => '_snippet_product_main_entity_of_page',
  //   'name' => 'mainEntityOfPage',
  //   'label' => __( 'Main Entity Of Page', '__x__' ),
  //   'description' => __( 'Indicates a page (or other CreativeWork) for which this thing is the main entity being described. See background notes for details. Inverse property: mainEntity.', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 36 =>
  // 37 =>
  // array (
  //   'id' => '_snippet_product_is_accessory_or_spare_part_for',
  //   'name' => 'isAccessoryOrSparePartFor',
  //   'label' => __( 'Is Accessory Or Spare Part For', '__x__' ),
  //   'description' => __( 'A pointer to another product (or multiple products) for which this product is an accessory or spare part. ', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 38 =>
  // array (
  //   'id' => '_snippet_product_is_based_on',
  //   'name' => 'isBasedOn',
  //   'label' => __( 'Is Based On', '__x__' ),
  //   'description' => __( 'A resource that was used in the creation of this resource. This term can be repeated for multiple sources. For example, http://example.com/great-multiplication-intro.html. Supersedes isBasedOnUrl.', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 39 =>
  // array (
  //   'id' => '_snippet_product_is_consumable_for',
  //   'name' => 'isConsumableFor',
  //   'label' => __( 'Is Consumable For', '__x__' ),
  //   'description' => __( 'A pointer to another product (or multiple products) for which this product is a consumable. ', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 40 =>
  // array (
  //   'id' => '_snippet_product_is_related_to',
  //   'name' => 'isRelatedTo',
  //   'label' => __( 'Is Related To', '__x__' ),
  //   'description' => __( 'A pointer to another, somehow related product (or multiple products). ', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 41 =>
  // array (
  //   'id' => '_snippet_product_is_similar_to',
  //   'name' => 'isSimilarTo',
  //   'label' => __( 'Is Similar To', '__x__' ),
  //   'description' => __( 'A pointer to another, functionally similar product (or multiple products). ', '__x__' ),
  //   'schema_type' => 'Product',
  //   'type' => 'text',
  // ),
  // 42 =>
  // array (
  //   'id' => '_snippet_product_item_offered',
  //   'name' => 'itemOffered',
  //   'label' => __( 'Item Offered', '__x__' ),
  //   'description' => __( 'The item being offered. ', '__x__' ),
  //   'schema_type' => 'Demand',
  //   'type' => 'text',
  // ),
  // 43 =>
  // array (
  //   'id' => '_snippet_product_item_shipped',
  //   'name' => 'itemShipped',
  //   'label' => __( 'Item Shipped', '__x__' ),
  //   'description' => __( 'Item(s) being shipped. ', '__x__' ),
  //   'schema_type' => 'ParcelDelivery',
  //   'type' => 'text',
  // ),
  // 44 =>
  // array (
  //   'id' => '_snippet_product_ordered_item',
  //   'name' => 'orderedItem',
  //   'label' => __( 'Ordered Item', '__x__' ),
  //   'description' => __( 'The item ordered. ', '__x__' ),
  //   'schema_type' => 'Order',
  //   'type' => 'text',
  // ),
  // 45 =>
  // array (
  //   'id' => '_snippet_product_owns',
  //   'name' => 'owns',
  //   'label' => __( 'Owns', '__x__' ),
  //   'description' => __( 'Products owned by the organization or person. ', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 46 =>
  // array (
  //   'id' => '_snippet_product_product_supported',
  //   'name' => 'productSupported',
  //   'label' => __( 'Product Supported', '__x__' ),
  //   'description' => __( 'The product or service this support contact point is related to (such as product support for a particular product line). This can be a specific product or product line (e.g. "iPhone") or a general category of products or services (e.g. "smartphones"). ', '__x__' ),
  //   'schema_type' => 'ContactPoint',
  //   'type' => 'text',
  // ),
  // 47 =>
  // array (
  //   'id' => '_snippet_product_type_of_good',
  //   'name' => 'typeOfGood',
  //   'label' => __( 'Type Of Good', '__x__' ),
  //   'description' => __( 'The product that this snippet value is referring to. ', '__x__' ),
  //   'schema_type' => 'OwnershipInfo',
  //   'type' => 'text',
  // ),
);
