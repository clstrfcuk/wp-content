<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-PLACE.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of Place
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Array of values
// =============================================================================

// Array of values
// =============================================================================

return array (
0 =>
array (
  'id' => '_snippet_place_type',
  'name' => '@type',
  'label' => __( 'Type', '__x__' ),
  'description' => __( 'Type of schema', '__x__' ),
  'schema_type' => '',
  'type' => 'type',
),
array (
  'id' => '_snippet_place_name',
  'name' => 'name',
  'label' => __( 'Name', '__x__' ),
  'description' => __( 'The name of the item.', '__x__' ),
  'schema_type' => 'Text',
  'type' => 'text',
),
array (
  'id' => '_snippet_place_address',
  'name' => 'address',
  'label' => __( 'Address', '__x__' ),
  'description' => __( 'Physical address of the item.', '__x__' ),
  'schema_type' => 'PostalAddress',
  'type' => 'postaladress',
),
//
//
// 1 =>
// array (
//   'id' => '_snippet_place_additional_property',
//   'name' => 'additionalProperty',
//   'label' => __( 'Additional Property', '__x__' ),
//   'description' => __( 'A property-value pair representing an additional characteristics of the entitity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.', '__x__' ),
//   'schema_type' => 'PropertyValue',
//   'type' => 'text',
// ),
// 2 =>
// 3 =>
// array (
//   'id' => '_snippet_place_aggregate_rating',
//   'name' => 'aggregateRating',
//   'label' => __( 'Aggregate Rating', '__x__' ),
//   'description' => __( 'The overall rating, based on a collection of reviews or ratings, of the item.', '__x__' ),
//   'schema_type' => 'AggregateRating',
//   'type' => 'text',
// ),
// 4 =>
// array (
//   'id' => '_snippet_place_amenity_feature',
//   'name' => 'amenityFeature',
//   'label' => __( 'Amenity Feature', '__x__' ),
//   'description' => __( 'An amenity feature (e.g. a characteristic or service) of the Accommodation. This generic property does not make a statement about whether the feature is included in an offer for the main accommodation or available at extra costs.', '__x__' ),
//   'schema_type' => 'LocationFeatureSpecification',
//   'type' => 'text',
// ),
// 5 =>
// array (
//   'id' => '_snippet_place_branch_code',
//   'name' => 'branchCode',
//   'label' => __( 'Branch Code', '__x__' ),
//   'description' => __( 'A short textual code (also called "store code") that uniquely identifies a place of business. The code is typically assigned by the parentOrganization and used in snippet URLs.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 6 =>
// array (
//   'id' => '_snippet_place_contained_in_place',
//   'name' => 'containedInPlace',
//   'label' => __( 'Contained In Place', '__x__' ),
//   'description' => __( 'The basic containment relation between a place and one that contains it. Supersedes containedIn. Inverse property: containsPlace.', '__x__' ),
//   'schema_type' => 'Place',
//   'type' => 'text',
// ),
// 7 =>
// array (
//   'id' => '_snippet_place_contains_place',
//   'name' => 'containsPlace',
//   'label' => __( 'Contains Place', '__x__' ),
//   'description' => __( 'The basic containment relation between a place and another that it contains. Inverse property: containedInPlace.', '__x__' ),
//   'schema_type' => 'Place',
//   'type' => 'text',
// ),
// 8 =>
// array (
//   'id' => '_snippet_place_event',
//   'name' => 'event',
//   'label' => __( 'Event', '__x__' ),
//   'description' => __( 'Upcoming or past event associated with this place, organization, or action. Supersedes events.', '__x__' ),
//   'schema_type' => 'Event',
//   'type' => 'text',
// ),
// 9 =>
// array (
//   'id' => '_snippet_place_fax_number',
//   'name' => 'faxNumber',
//   'label' => __( 'Fax Number', '__x__' ),
//   'description' => __( 'The fax number.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 10 =>
// array (
//   'id' => '_snippet_place_geo',
//   'name' => 'geo',
//   'label' => __( 'Geo', '__x__' ),
//   'description' => __( 'The geo coordinates of the place.', '__x__' ),
//   'schema_type' => 'GeoCoordinates',
//   'type' => 'text',
// ),
// 11 =>
// array (
//   'id' => '_snippet_place_global_location_number',
//   'name' => 'globalLocationNumber',
//   'label' => __( 'Global Location Number', '__x__' ),
//   'description' => __( 'The Global Location Number (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place. The GLN is a 13-digit number used to identify parties and physical locations.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 12 =>
// array (
//   'id' => '_snippet_place_has_map',
//   'name' => 'hasMap',
//   'label' => __( 'Has Map', '__x__' ),
//   'description' => __( 'A URL to a map of the place. Supersedes map, maps.', '__x__' ),
//   'schema_type' => 'Map',
//   'type' => 'text',
// ),
// 13 =>
// array (
//   'id' => '_snippet_place_isic_v4',
//   'name' => 'isicV4',
//   'label' => __( 'Isic V4', '__x__' ),
//   'description' => __( 'The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 14 =>
// array (
//   'id' => '_snippet_place_logo',
//   'name' => 'logo',
//   'label' => __( 'Logo', '__x__' ),
//   'description' => __( 'An associated logo.', '__x__' ),
//   'schema_type' => 'ImageObject',
//   'type' => 'text',
// ),
// 15 =>
// array (
//   'id' => '_snippet_place_opening_hours_specification',
//   'name' => 'openingHoursSpecification',
//   'label' => __( 'Opening Hours Specification', '__x__' ),
//   'description' => __( 'The opening hours of a certain place.', '__x__' ),
//   'schema_type' => 'OpeningHoursSpecification',
//   'type' => 'text',
// ),
// 16 =>
// array (
//   'id' => '_snippet_place_photo',
//   'name' => 'photo',
//   'label' => __( 'Photo', '__x__' ),
//   'description' => __( 'A photograph of this place. Supersedes photos.', '__x__' ),
//   'schema_type' => 'ImageObject',
//   'type' => 'text',
// ),
// 17 =>
// array (
//   'id' => '_snippet_place_review',
//   'name' => 'review',
//   'label' => __( 'Review', '__x__' ),
//   'description' => __( 'A review of the item. Supersedes reviews.', '__x__' ),
//   'schema_type' => 'Review',
//   'type' => 'text',
// ),
// 18 =>
// array (
//   'id' => '_snippet_place_smoking_allowed',
//   'name' => 'smokingAllowed',
//   'label' => __( 'Smoking Allowed', '__x__' ),
//   'description' => __( 'Indicates whether it is allowed to smoke in the place, e.g. in the restaurant, hotel or hotel room.', '__x__' ),
//   'schema_type' => 'Boolean',
//   'type' => 'text',
// ),
// 19 =>
// array (
//   'id' => '_snippet_place_special_opening_hours_specification',
//   'name' => 'specialOpeningHoursSpecification',
//   'label' => __( 'Special Opening Hours Specification', '__x__' ),
//   'description' => __( 'The special opening hours of a certain place.', '__x__' ),
//   'schema_type' => 'OpeningHoursSpecification',
//   'type' => 'text',
// ),
// 20 =>
// array (
//   'id' => '_snippet_place_telephone',
//   'name' => 'telephone',
//   'label' => __( 'Telephone', '__x__' ),
//   'description' => __( 'The telephone number.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 21 =>
// array (
//   'id' => '_snippet_place_additional_type',
//   'name' => 'additionalType',
//   'label' => __( 'Additional Type', '__x__' ),
//   'description' => __( 'An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax. This is a relationship between something and a class that the thing is in. In RDFa syntax, it is better to use the native RDFa syntax - the "typeof" attribute - for multiple types. Schema.org tools may have only weaker understanding of extra types, in particular those defined externally.', '__x__' ),
//   'schema_type' => 'URL',
//   'type' => 'text',
// ),
// 22 =>
// array (
//   'id' => '_snippet_place_alternate_name',
//   'name' => 'alternateName',
//   'label' => __( 'Alternate Name', '__x__' ),
//   'description' => __( 'An alias for the item.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 23 =>
// array (
//   'id' => '_snippet_place_description',
//   'name' => 'description',
//   'label' => __( 'Description', '__x__' ),
//   'description' => __( 'A description of the item.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 24 =>
// array (
//   'id' => '_snippet_place_disambiguating_description',
//   'name' => 'disambiguatingDescription',
//   'label' => __( 'Disambiguating Description', '__x__' ),
//   'description' => __( 'A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.', '__x__' ),
//   'schema_type' => 'Text',
//   'type' => 'text',
// ),
// 25 =>
// array (
//   'id' => '_snippet_place_image',
//   'name' => 'image',
//   'label' => __( 'Image', '__x__' ),
//   'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
//   'schema_type' => 'ImageObject',
//   'type' => 'text',
// ),
// 26 =>
// array (
//   'id' => '_snippet_place_main_entity_of_page',
//   'name' => 'mainEntityOfPage',
//   'label' => __( 'Main Entity Of Page', '__x__' ),
//   'description' => __( 'Indicates a page (or other CreativeWork) for which this thing is the main entity being described. See background notes for details. Inverse property: mainEntity.', '__x__' ),
//   'schema_type' => 'CreativeWork',
//   'type' => 'text',
// ),
// 27 =>
// 28 =>
// array (
//   'id' => '_snippet_place_potential_action',
//   'name' => 'potentialAction',
//   'label' => __( 'Potential Action', '__x__' ),
//   'description' => __( 'Indicates a potential Action, which describes an idealized action in which this thing would play an "object" role.', '__x__' ),
//   'schema_type' => 'Action',
//   'type' => 'text',
// ),
// 29 =>
// array (
//   'id' => '_snippet_place_same_as',
//   'name' => 'sameAs',
//   'label' => __( 'Same As', '__x__' ),
//   'description' => __( 'URL of a reference Web page that unambiguously indicates the item"s identity. E.g. the URL of the item"s Wikipedia page, Freebase page, or official website.', '__x__' ),
//   'schema_type' => 'URL',
//   'type' => 'text',
// ),
// 30 =>
// array (
//   'id' => '_snippet_place_url',
//   'name' => 'url',
//   'label' => __( 'Url', '__x__' ),
//   'description' => __( 'URL of the item.', '__x__' ),
//   'schema_type' => 'URL',
//   'type' => 'text',
// ),
// 31 =>
// array (
//   'id' => '_snippet_place_area_served',
//   'name' => 'areaServed',
//   'label' => __( 'Area Served', '__x__' ),
//   'description' => __( 'The geographic area where a service or offered item is provided. Supersedes serviceArea.', '__x__' ),
//   'schema_type' => 'ContactPoint',
//   'type' => 'text',
// ),
// 32 =>
// array (
//   'id' => '_snippet_place_available_at_or_from',
//   'name' => 'availableAtOrFrom',
//   'label' => __( 'Available At Or From', '__x__' ),
//   'description' => __( 'The place(s) from which the offer can be obtained (e.g. store locations). ', '__x__' ),
//   'schema_type' => 'Demand',
//   'type' => 'text',
// ),
// 33 =>
// array (
//   'id' => '_snippet_place_birth_place',
//   'name' => 'birthPlace',
//   'label' => __( 'Birth Place', '__x__' ),
//   'description' => __( 'The place where the person was born. ', '__x__' ),
//   'schema_type' => 'Person',
//   'type' => 'text',
// ),
// 34 =>
// array (
//   'id' => '_snippet_place_contained_in_place',
//   'name' => 'containedInPlace',
//   'label' => __( 'Contained In Place', '__x__' ),
//   'description' => __( 'The basic containment relation between a place and one that contains it. Supersedes containedIn. inverse property: containsPlace.', '__x__' ),
//   'schema_type' => 'Place',
//   'type' => 'text',
// ),
// 35 =>
// array (
//   'id' => '_snippet_place_contains_place',
//   'name' => 'containsPlace',
//   'label' => __( 'Contains Place', '__x__' ),
//   'description' => __( 'The basic containment relation between a place and another that it contains.  inverse property: containedInPlace.', '__x__' ),
//   'schema_type' => 'Place',
//   'type' => 'text',
// ),
// 36 =>
// array (
//   'id' => '_snippet_place_content_location',
//   'name' => 'contentLocation',
//   'label' => __( 'Content Location', '__x__' ),
//   'description' => __( 'The location depicted or described in the content. For example, the location in a photograph or painting. ', '__x__' ),
//   'schema_type' => 'CreativeWork',
//   'type' => 'text',
// ),
// 37 =>
// array (
//   'id' => '_snippet_place_death_place',
//   'name' => 'deathPlace',
//   'label' => __( 'Death Place', '__x__' ),
//   'description' => __( 'The place where the person died. ', '__x__' ),
//   'schema_type' => 'Person',
//   'type' => 'text',
// ),
// 38 =>
// array (
//   'id' => '_snippet_place_dropoff_location',
//   'name' => 'dropoffLocation',
//   'label' => __( 'Dropoff Location', '__x__' ),
//   'description' => __( 'Where a rental car can be dropped off. ', '__x__' ),
//   'schema_type' => 'RentalCarReservation',
//   'type' => 'text',
// ),
// 39 =>
// array (
//   'id' => '_snippet_place_eligible_region',
//   'name' => 'eligibleRegion',
//   'label' => __( 'Eligible Region', '__x__' ),
//   'description' => __( 'The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is valid.', '__x__' ),
//   'schema_type' => 'DeliveryChargeSpecification',
//   'type' => 'text',
// ),
// 40 =>
// array (
//   'id' => '_snippet_place_exercise_course',
//   'name' => 'exerciseCourse',
//   'label' => __( 'Exercise Course', '__x__' ),
//   'description' => __( 'A sub property of location. The course where this action was taken. Supersedes course.', '__x__' ),
//   'schema_type' => 'ExerciseAction',
//   'type' => 'text',
// ),
// 41 =>
// array (
//   'id' => '_snippet_place_food_establishment',
//   'name' => 'foodEstablishment',
//   'label' => __( 'Food Establishment', '__x__' ),
//   'description' => __( 'A sub property of location. The specific food establishment where the action occurred. ', '__x__' ),
//   'schema_type' => 'CookAction',
//   'type' => 'text',
// ),
// 42 =>
// array (
//   'id' => '_snippet_place_founding_location',
//   'name' => 'foundingLocation',
//   'label' => __( 'Founding Location', '__x__' ),
//   'description' => __( 'The place where the Organization was founded. ', '__x__' ),
//   'schema_type' => 'Organization',
//   'type' => 'text',
// ),
// 43 =>
// array (
//   'id' => '_snippet_place_from_location',
//   'name' => 'fromLocation',
//   'label' => __( 'From Location', '__x__' ),
//   'description' => __( 'A sub property of location. The original location of the object or the agent before the action. ', '__x__' ),
//   'schema_type' => 'ExerciseAction',
//   'type' => 'text',
// ),
// 44 =>
// array (
//   'id' => '_snippet_place_game_location',
//   'name' => 'gameLocation',
//   'label' => __( 'Game Location', '__x__' ),
//   'description' => __( 'Real or fictional location of the game (or part of game). ', '__x__' ),
//   'schema_type' => 'Game',
//   'type' => 'text',
// ),
// 45 =>
// array (
//   'id' => '_snippet_place_has_pos',
//   'name' => 'hasPOS',
//   'label' => __( 'Has POS', '__x__' ),
//   'description' => __( 'Points-of-Sales operated by the organization or person. ', '__x__' ),
//   'schema_type' => 'Organization',
//   'type' => 'text',
// ),
// 46 =>
// array (
//   'id' => '_snippet_place_home_location',
//   'name' => 'homeLocation',
//   'label' => __( 'Home Location', '__x__' ),
//   'description' => __( 'A contact location for a person"s residence. ', '__x__' ),
//   'schema_type' => 'Person',
//   'type' => 'text',
// ),
// 47 =>
// array (
//   'id' => '_snippet_place_ineligible_region',
//   'name' => 'ineligibleRegion',
//   'label' => __( 'Ineligible Region', '__x__' ),
//   'description' => __( 'The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid, e.g. a region where the transaction is not allowed.', '__x__' ),
//   'schema_type' => 'DeliveryChargeSpecification',
//   'type' => 'text',
// ),
// 48 =>
// array (
//   'id' => '_snippet_place_job_location',
//   'name' => 'jobLocation',
//   'label' => __( 'Job Location', '__x__' ),
//   'description' => __( 'A (typically single) geographic location associated with the job position. ', '__x__' ),
//   'schema_type' => 'JobPosting',
//   'type' => 'text',
// ),
// 49 =>
// array (
//   'id' => '_snippet_place_location',
//   'name' => 'location',
//   'label' => __( 'Location', '__x__' ),
//   'description' => __( 'The location of for example where the event is happening, an organization is located, or where an action takes place. ', '__x__' ),
//   'schema_type' => 'Action',
//   'type' => 'text',
// ),
// 50 =>
// array (
//   'id' => '_snippet_place_location_created',
//   'name' => 'locationCreated',
//   'label' => __( 'Location Created', '__x__' ),
//   'description' => __( 'The location where the CreativeWork was created, which may not be the same as the location depicted in the CreativeWork. ', '__x__' ),
//   'schema_type' => 'CreativeWork',
//   'type' => 'text',
// ),
// 51 =>
// array (
//   'id' => '_snippet_place_pickup_location',
//   'name' => 'pickupLocation',
//   'label' => __( 'Pickup Location', '__x__' ),
//   'description' => __( 'Where a taxi will pick up a passenger or a rental car can be picked up. ', '__x__' ),
//   'schema_type' => 'RentalCarReservation',
//   'type' => 'text',
// ),
// 52 =>
// array (
//   'id' => '_snippet_place_regions_allowed',
//   'name' => 'regionsAllowed',
//   'label' => __( 'Regions Allowed', '__x__' ),
//   'description' => __( 'The regions where the media is allowed. If not specified, then it"s assumed to be allowed everywhere. Specify the countries in ISO 3166 format. ', '__x__' ),
//   'schema_type' => 'MediaObject',
//   'type' => 'text',
// ),
// 53 =>
// array (
//   'id' => '_snippet_place_service_location',
//   'name' => 'serviceLocation',
//   'label' => __( 'Service Location', '__x__' ),
//   'description' => __( 'The location (e.g. civic structure, local business, etc.) where a person can go to access the service. ', '__x__' ),
//   'schema_type' => 'ServiceChannel',
//   'type' => 'text',
// ),
// 54 =>
// array (
//   'id' => '_snippet_place_spatial_coverage',
//   'name' => 'spatialCoverage',
//   'label' => __( 'Spatial Coverage', '__x__' ),
//   'description' => __( 'The spatialCoverage of a CreativeWork indicates the place(s) which are the focus of the content. It is a subproperty of contentLocation intended primarily for more technical and detailed materials. For example with a Dataset, it indicates areas that the dataset describes: a dataset of New York weather would have spatialCoverage which was the place: the state of New York. Supersedes spatial.', '__x__' ),
//   'schema_type' => 'CreativeWork',
//   'type' => 'text',
// ),
// 55 =>
// array (
//   'id' => '_snippet_place_to_location',
//   'name' => 'toLocation',
//   'label' => __( 'To Location', '__x__' ),
//   'description' => __( 'A sub property of location. The final location of the object or the agent after the action. ', '__x__' ),
//   'schema_type' => 'ExerciseAction',
//   'type' => 'text',
// ),
// 56 =>
// array (
//   'id' => '_snippet_place_work_location',
//   'name' => 'workLocation',
//   'label' => __( 'Work Location', '__x__' ),
//   'description' => __( 'A contact location for a person"s place of work. ', '__x__' ),
//   'schema_type' => 'Person',
//   'type' => 'text',
// ),
);
