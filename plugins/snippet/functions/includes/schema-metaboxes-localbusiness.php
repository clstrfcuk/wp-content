<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-LOCALBUSINESS.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of LocalBusiness
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
    'id' => '_snippet_localbusiness_type',
    'name' => '@type',
    'label' => __( 'Type', '__x__' ),
    'description' => __( 'Type of schema', '__x__' ),
    'schema_type' => '',
    'type' => 'type',
  ),
  1 =>
  array (
    'id' => '_snippet_localbusiness_currencies_accepted',
    'name' => 'currenciesAccepted',
    'label' => __( 'Currencies Accepted', '__x__' ),
    'description' => __( 'The currency accepted (in ISO 4217 currency format).', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  2 =>
  array (
    'id' => '_snippet_localbusiness_opening_hours',
    'name' => 'openingHours',
    'label' => __( 'Opening Hours', '__x__' ),
    'description' => __( 'The general opening hours for a business. Opening hours can be specified as a weekly time range, starting with days, then times per day. Multiple days can be listed with commas "," separating each day. Day or time ranges are specified using a hyphen "-".  Days are specified using the following two-letter combinations: Mo, Tu, We, Th, Fr, Sa, Su. Times are specified using 24:00 time. For example, 3pm is specified as 15:00.  Here is an example: &lt;time itemprop="openingHours" datetime=&quot;Tu,Th 16:00-20:00&quot;&gt;Tuesdays and Thursdays 4-8pm&lt;/time&gt;. If a business is open 7 days a week, then it can be specified as &lt;time itemprop=&quot;openingHours&quot; datetime=&quot;Mo-Su&quot;&gt;Monday through Sunday, all day&lt;/time&gt;.  ', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  3 =>
  array (
    'id' => '_snippet_localbusiness_payment_accepted',
    'name' => 'paymentAccepted',
    'label' => __( 'Payment Accepted', '__x__' ),
    'description' => __( 'Cash, credit card, etc.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  4 =>
  array (
    'id' => '_snippet_localbusiness_price_range',
    'name' => 'priceRange',
    'label' => __( 'Price Range', '__x__' ),
    'description' => __( 'The price range of the business, for example $$$.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  5 =>
  array (
    'id' => '_snippet_localbusiness_additional_property',
    'name' => 'additionalProperty',
    'label' => __( 'Additional Property', '__x__' ),
    'description' => __( 'A property-value pair representing an additional characteristics of the entitity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.', '__x__' ),
    'schema_type' => 'PropertyValue',
    'type' => 'text',
  ),
  6 =>
  array (
    'id' => '_snippet_localbusiness_address',
    'name' => 'address',
    'label' => __( 'Address', '__x__' ),
    'description' => __( 'Physical address of the item.', '__x__' ),
    'schema_type' => 'PostalAddress',
    'type' => 'text',
  ),
  7 =>
  array (
    'id' => '_snippet_localbusiness_aggregate_rating',
    'name' => 'aggregateRating',
    'label' => __( 'Aggregate Rating', '__x__' ),
    'description' => __( 'The overall rating, based on a collection of reviews or ratings, of the item.', '__x__' ),
    'schema_type' => 'AggregateRating',
    'type' => 'text',
  ),
  8 =>
  array (
    'id' => '_snippet_localbusiness_amenity_feature',
    'name' => 'amenityFeature',
    'label' => __( 'Amenity Feature', '__x__' ),
    'description' => __( 'An amenity feature (e.g. a characteristic or service) of the Accommodation. This generic property does not make a statement about whether the feature is included in an offer for the main accommodation or available at extra costs.', '__x__' ),
    'schema_type' => 'LocationFeatureSpecification',
    'type' => 'text',
  ),
  9 =>
  array (
    'id' => '_snippet_localbusiness_branch_code',
    'name' => 'branchCode',
    'label' => __( 'Branch Code', '__x__' ),
    'description' => __( 'A short textual code (also called "store code") that uniquely identifies a place of business. The code is typically assigned by the parentOrganization and used in snippet URLs.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  10 =>
  array (
    'id' => '_snippet_localbusiness_contained_in_place',
    'name' => 'containedInPlace',
    'label' => __( 'Contained In Place', '__x__' ),
    'description' => __( 'The basic containment relation between a place and one that contains it. Supersedes containedIn. Inverse property: containsPlace.', '__x__' ),
    'schema_type' => 'Place',
    'type' => 'text',
  ),
  11 =>
  array (
    'id' => '_snippet_localbusiness_contains_place',
    'name' => 'containsPlace',
    'label' => __( 'Contains Place', '__x__' ),
    'description' => __( 'The basic containment relation between a place and another that it contains. Inverse property: containedInPlace.', '__x__' ),
    'schema_type' => 'Place',
    'type' => 'text',
  ),
  12 =>
  array (
    'id' => '_snippet_localbusiness_event',
    'name' => 'event',
    'label' => __( 'Event', '__x__' ),
    'description' => __( 'Upcoming or past event associated with this place, organization, or action. Supersedes events.', '__x__' ),
    'schema_type' => 'Event',
    'type' => 'text',
  ),
  13 =>
  array (
    'id' => '_snippet_localbusiness_fax_number',
    'name' => 'faxNumber',
    'label' => __( 'Fax Number', '__x__' ),
    'description' => __( 'The fax number.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  14 =>
  array (
    'id' => '_snippet_localbusiness_geo',
    'name' => 'geo',
    'label' => __( 'Geo', '__x__' ),
    'description' => __( 'The geo coordinates of the place.', '__x__' ),
    'schema_type' => 'GeoCoordinates',
    'type' => 'text',
  ),
  15 =>
  array (
    'id' => '_snippet_localbusiness_global_location_number',
    'name' => 'globalLocationNumber',
    'label' => __( 'Global Location Number', '__x__' ),
    'description' => __( 'The Global Location Number (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place. The GLN is a 13-digit number used to identify parties and physical locations.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  16 =>
  array (
    'id' => '_snippet_localbusiness_has_map',
    'name' => 'hasMap',
    'label' => __( 'Has Map', '__x__' ),
    'description' => __( 'A URL to a map of the place. Supersedes map, maps.', '__x__' ),
    'schema_type' => 'Map',
    'type' => 'text',
  ),
  17 =>
  array (
    'id' => '_snippet_localbusiness_isic_v4',
    'name' => 'isicV4',
    'label' => __( 'Isic V4', '__x__' ),
    'description' => __( 'The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  18 =>
  array (
    'id' => '_snippet_localbusiness_logo',
    'name' => 'logo',
    'label' => __( 'Logo', '__x__' ),
    'description' => __( 'An associated logo.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'text',
  ),
  19 =>
  array (
    'id' => '_snippet_localbusiness_opening_hours_specification',
    'name' => 'openingHoursSpecification',
    'label' => __( 'Opening Hours Specification', '__x__' ),
    'description' => __( 'The opening hours of a certain place.', '__x__' ),
    'schema_type' => 'OpeningHoursSpecification',
    'type' => 'text',
  ),
  20 =>
  array (
    'id' => '_snippet_localbusiness_photo',
    'name' => 'photo',
    'label' => __( 'Photo', '__x__' ),
    'description' => __( 'A photograph of this place. Supersedes photos.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'text',
  ),
  21 =>
  array (
    'id' => '_snippet_localbusiness_review',
    'name' => 'review',
    'label' => __( 'Review', '__x__' ),
    'description' => __( 'A review of the item. Supersedes reviews.', '__x__' ),
    'schema_type' => 'Review',
    'type' => 'text',
  ),
  22 =>
  array (
    'id' => '_snippet_localbusiness_smoking_allowed',
    'name' => 'smokingAllowed',
    'label' => __( 'Smoking Allowed', '__x__' ),
    'description' => __( 'Indicates whether it is allowed to smoke in the place, e.g. in the restaurant, hotel or hotel room.', '__x__' ),
    'schema_type' => 'Boolean',
    'type' => 'text',
  ),
  23 =>
  array (
    'id' => '_snippet_localbusiness_special_opening_hours_specification',
    'name' => 'specialOpeningHoursSpecification',
    'label' => __( 'Special Opening Hours Specification', '__x__' ),
    'description' => __( 'The special opening hours of a certain place.', '__x__' ),
    'schema_type' => 'OpeningHoursSpecification',
    'type' => 'text',
  ),
  24 =>
  array (
    'id' => '_snippet_localbusiness_telephone',
    'name' => 'telephone',
    'label' => __( 'Telephone', '__x__' ),
    'description' => __( 'The telephone number.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  25 =>
  array (
    'id' => '_snippet_localbusiness_address',
    'name' => 'address',
    'label' => __( 'Address', '__x__' ),
    'description' => __( 'Physical address of the item.', '__x__' ),
    'schema_type' => 'PostalAddress',
    'type' => 'text',
  ),
  26 =>
  array (
    'id' => '_snippet_localbusiness_aggregate_rating',
    'name' => 'aggregateRating',
    'label' => __( 'Aggregate Rating', '__x__' ),
    'description' => __( 'The overall rating, based on a collection of reviews or ratings, of the item.', '__x__' ),
    'schema_type' => 'AggregateRating',
    'type' => 'text',
  ),
  27 =>
  array (
    'id' => '_snippet_localbusiness_alumni',
    'name' => 'alumni',
    'label' => __( 'Alumni', '__x__' ),
    'description' => __( 'Alumni of an organization. Inverse property: alumniOf.', '__x__' ),
    'schema_type' => 'Person',
    'type' => 'text',
  ),
  28 =>
  array (
    'id' => '_snippet_localbusiness_area_served',
    'name' => 'areaServed',
    'label' => __( 'Area Served', '__x__' ),
    'description' => __( 'The geographic area where a service or offered item is provided. Supersedes serviceArea.', '__x__' ),
    'schema_type' => 'AdministrativeArea',
    'type' => 'text',
  ),
  29 =>
  array (
    'id' => '_snippet_localbusiness_award',
    'name' => 'award',
    'label' => __( 'Award', '__x__' ),
    'description' => __( 'An award won by or for this item. Supersedes awards.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  30 =>
  array (
    'id' => '_snippet_localbusiness_brand',
    'name' => 'brand',
    'label' => __( 'Brand', '__x__' ),
    'description' => __( 'The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.', '__x__' ),
    'schema_type' => 'Brand',
    'type' => 'text',
  ),
  31 =>
  array (
    'id' => '_snippet_localbusiness_contact_point',
    'name' => 'contactPoint',
    'label' => __( 'Contact Point', '__x__' ),
    'description' => __( 'A contact point for a person or organization. Supersedes contactPoints.', '__x__' ),
    'schema_type' => 'ContactPoint',
    'type' => 'text',
  ),
  32 =>
  array (
    'id' => '_snippet_localbusiness_department',
    'name' => 'department',
    'label' => __( 'Department', '__x__' ),
    'description' => __( 'A relationship between an organization and a department of that organization, also described as an organization (allowing different urls, logos, opening hours). For example: a store with a pharmacy, or a bakery with a cafe.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  33 =>
  array (
    'id' => '_snippet_localbusiness_dissolution_date',
    'name' => 'dissolutionDate',
    'label' => __( 'Dissolution Date', '__x__' ),
    'description' => __( 'The date that this organization was dissolved.', '__x__' ),
    'schema_type' => 'Date',
    'type' => 'text',
  ),
  34 =>
  array (
    'id' => '_snippet_localbusiness_duns',
    'name' => 'duns',
    'label' => __( 'Duns', '__x__' ),
    'description' => __( 'The Dun &amp; Bradstreet DUNS number for identifying an organization or business person.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  35 =>
  array (
    'id' => '_snippet_localbusiness_email',
    'name' => 'email',
    'label' => __( 'Email', '__x__' ),
    'description' => __( 'Email address.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  36 =>
  array (
    'id' => '_snippet_localbusiness_employee',
    'name' => 'employee',
    'label' => __( 'Employee', '__x__' ),
    'description' => __( 'Someone working for this organization. Supersedes employees.', '__x__' ),
    'schema_type' => 'Person',
    'type' => 'text',
  ),
  37 =>
  array (
    'id' => '_snippet_localbusiness_event',
    'name' => 'event',
    'label' => __( 'Event', '__x__' ),
    'description' => __( 'Upcoming or past event associated with this place, organization, or action. Supersedes events.', '__x__' ),
    'schema_type' => 'Event',
    'type' => 'text',
  ),
  38 =>
  array (
    'id' => '_snippet_localbusiness_fax_number',
    'name' => 'faxNumber',
    'label' => __( 'Fax Number', '__x__' ),
    'description' => __( 'The fax number.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  39 =>
  array (
    'id' => '_snippet_localbusiness_founder',
    'name' => 'founder',
    'label' => __( 'Founder', '__x__' ),
    'description' => __( 'A person who founded this organization. Supersedes founders.', '__x__' ),
    'schema_type' => 'Person',
    'type' => 'text',
  ),
  40 =>
  array (
    'id' => '_snippet_localbusiness_founding_date',
    'name' => 'foundingDate',
    'label' => __( 'Founding Date', '__x__' ),
    'description' => __( 'The date that this organization was founded.', '__x__' ),
    'schema_type' => 'Date',
    'type' => 'text',
  ),
  41 =>
  array (
    'id' => '_snippet_localbusiness_founding_location',
    'name' => 'foundingLocation',
    'label' => __( 'Founding Location', '__x__' ),
    'description' => __( 'The place where the Organization was founded.', '__x__' ),
    'schema_type' => 'Place',
    'type' => 'text',
  ),
  42 =>
  array (
    'id' => '_snippet_localbusiness_funder',
    'name' => 'funder',
    'label' => __( 'Funder', '__x__' ),
    'description' => __( 'A person or organization that supports (sponsors) something through some kind of financial contribution.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  43 =>
  array (
    'id' => '_snippet_localbusiness_global_location_number',
    'name' => 'globalLocationNumber',
    'label' => __( 'Global Location Number', '__x__' ),
    'description' => __( 'The Global Location Number (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place. The GLN is a 13-digit number used to identify parties and physical locations.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  44 =>
  array (
    'id' => '_snippet_localbusiness_has_offer_catalog',
    'name' => 'hasOfferCatalog',
    'label' => __( 'Has Offer Catalog', '__x__' ),
    'description' => __( 'Indicates an OfferCatalog listing for this Organization, Person, or Service.', '__x__' ),
    'schema_type' => 'OfferCatalog',
    'type' => 'text',
  ),
  45 =>
  array (
    'id' => '_snippet_localbusiness_has_pos',
    'name' => 'hasPOS',
    'label' => __( 'Has POS', '__x__' ),
    'description' => __( 'Points-of-Sales operated by the organization or person.', '__x__' ),
    'schema_type' => 'Place',
    'type' => 'text',
  ),
  46 =>
  array (
    'id' => '_snippet_localbusiness_isic_v4',
    'name' => 'isicV4',
    'label' => __( 'Isic V4', '__x__' ),
    'description' => __( 'The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  47 =>
  array (
    'id' => '_snippet_localbusiness_legal_name',
    'name' => 'legalName',
    'label' => __( 'Legal Name', '__x__' ),
    'description' => __( 'The official name of the organization, e.g. the registered company name.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  48 =>
  array (
    'id' => '_snippet_localbusiness_lei_code',
    'name' => 'leiCode',
    'label' => __( 'Lei Code', '__x__' ),
    'description' => __( 'An organization identifier that uniquely identifies a legal entity as defined in ISO 17442.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  49 =>
  array (
    'id' => '_snippet_localbusiness_location',
    'name' => 'location',
    'label' => __( 'Location', '__x__' ),
    'description' => __( 'The location of for example where the event is happening, an organization is located, or where an action takes place.', '__x__' ),
    'schema_type' => 'Place',
    'type' => 'text',
  ),
  50 =>
  array (
    'id' => '_snippet_localbusiness_logo',
    'name' => 'logo',
    'label' => __( 'Logo', '__x__' ),
    'description' => __( 'An associated logo.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'text',
  ),
  51 =>
  array (
    'id' => '_snippet_localbusiness_makes_offer',
    'name' => 'makesOffer',
    'label' => __( 'Makes Offer', '__x__' ),
    'description' => __( 'A pointer to products or services offered by the organization or person. Inverse property: offeredBy.', '__x__' ),
    'schema_type' => 'Offer',
    'type' => 'text',
  ),
  52 =>
  array (
    'id' => '_snippet_localbusiness_member',
    'name' => 'member',
    'label' => __( 'Member', '__x__' ),
    'description' => __( 'A member of an Organization or a ProgramMembership. Organizations can be members of organizations; ProgramMembership is typically for individuals. Supersedes members, musicGroupMember. Inverse property: memberOf.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  53 =>
  array (
    'id' => '_snippet_localbusiness_member_of',
    'name' => 'memberOf',
    'label' => __( 'Member Of', '__x__' ),
    'description' => __( 'An Organization (or ProgramMembership) to which this Person or Organization belongs. Inverse property: member.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  54 =>
  array (
    'id' => '_snippet_localbusiness_naics',
    'name' => 'naics',
    'label' => __( 'Naics', '__x__' ),
    'description' => __( 'The North American Industry Classification System (NAICS) code for a particular organization or business person.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  55 =>
  array (
    'id' => '_snippet_localbusiness_number_of_employees',
    'name' => 'numberOfEmployees',
    'label' => __( 'Number Of Employees', '__x__' ),
    'description' => __( 'The number of employees in an organization e.g. business.', '__x__' ),
    'schema_type' => 'QuantitativeValue',
    'type' => 'text',
  ),
  56 =>
  array (
    'id' => '_snippet_localbusiness_owns',
    'name' => 'owns',
    'label' => __( 'Owns', '__x__' ),
    'description' => __( 'Products owned by the organization or person.', '__x__' ),
    'schema_type' => 'OwnershipInfo',
    'type' => 'text',
  ),
  57 =>
  array (
    'id' => '_snippet_localbusiness_parent_organization',
    'name' => 'parentOrganization',
    'label' => __( 'Parent Organization', '__x__' ),
    'description' => __( 'The larger organization that this local business is a branch of, if any. Supersedes branchOf. Inverse property: subOrganization.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  58 =>
  array (
    'id' => '_snippet_localbusiness_review',
    'name' => 'review',
    'label' => __( 'Review', '__x__' ),
    'description' => __( 'A review of the item. Supersedes reviews.', '__x__' ),
    'schema_type' => 'Review',
    'type' => 'text',
  ),
  59 =>
  array (
    'id' => '_snippet_localbusiness_seeks',
    'name' => 'seeks',
    'label' => __( 'Seeks', '__x__' ),
    'description' => __( 'A pointer to products or services sought by the organization or person (demand).', '__x__' ),
    'schema_type' => 'Demand',
    'type' => 'text',
  ),
  60 =>
  array (
    'id' => '_snippet_localbusiness_sponsor',
    'name' => 'sponsor',
    'label' => __( 'Sponsor', '__x__' ),
    'description' => __( 'A person or organization that supports a thing through a pledge, promise, or financial contribution. e.g. a sponsor of a Medical Study or a corporate sponsor of an event.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  61 =>
  array (
    'id' => '_snippet_localbusiness_sub_organization',
    'name' => 'subOrganization',
    'label' => __( 'Sub Organization', '__x__' ),
    'description' => __( 'A relationship between two organizations where the first includes the second, e.g., as a subsidiary. See also: the more specific "department" property. Inverse property: parentOrganization.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  62 =>
  array (
    'id' => '_snippet_localbusiness_tax_id',
    'name' => 'taxID',
    'label' => __( 'Tax ID', '__x__' ),
    'description' => __( 'The Tax / Fiscal ID of the organization or person, e.g. the TIN in the US or the CIF/NIF in Spain.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  63 =>
  array (
    'id' => '_snippet_localbusiness_telephone',
    'name' => 'telephone',
    'label' => __( 'Telephone', '__x__' ),
    'description' => __( 'The telephone number.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  64 =>
  array (
    'id' => '_snippet_localbusiness_vat_id',
    'name' => 'vatID',
    'label' => __( 'Vat ID', '__x__' ),
    'description' => __( 'The Value-added Tax ID of the organization or person.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  65 =>
  array (
    'id' => '_snippet_localbusiness_additional_type',
    'name' => 'additionalType',
    'label' => __( 'Additional Type', '__x__' ),
    'description' => __( 'An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax. This is a relationship between something and a class that the thing is in. In RDFa syntax, it is better to use the native RDFa syntax - the "typeof" attribute - for multiple types. Schema.org tools may have only weaker understanding of extra types, in particular those defined externally.', '__x__' ),
    'schema_type' => 'URL',
    'type' => 'text',
  ),
  66 =>
  array (
    'id' => '_snippet_localbusiness_alternate_name',
    'name' => 'alternateName',
    'label' => __( 'Alternate Name', '__x__' ),
    'description' => __( 'An alias for the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  67 =>
  array (
    'id' => '_snippet_localbusiness_description',
    'name' => 'description',
    'label' => __( 'Description', '__x__' ),
    'description' => __( 'A description of the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  68 =>
  array (
    'id' => '_snippet_localbusiness_disambiguating_description',
    'name' => 'disambiguatingDescription',
    'label' => __( 'Disambiguating Description', '__x__' ),
    'description' => __( 'A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  69 =>
  array (
    'id' => '_snippet_localbusiness_image',
    'name' => 'image',
    'label' => __( 'Image', '__x__' ),
    'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'text',
  ),
  70 =>
  array (
    'id' => '_snippet_localbusiness_main_entity_of_page',
    'name' => 'mainEntityOfPage',
    'label' => __( 'Main Entity Of Page', '__x__' ),
    'description' => __( 'Indicates a page (or other CreativeWork) for which this thing is the main entity being described. See background notes for details. Inverse property: mainEntity.', '__x__' ),
    'schema_type' => 'CreativeWork',
    'type' => 'text',
  ),
  71 =>
  array (
    'id' => '_snippet_localbusiness_name',
    'name' => 'name',
    'label' => __( 'Name', '__x__' ),
    'description' => __( 'The name of the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  72 =>
  array (
    'id' => '_snippet_localbusiness_potential_action',
    'name' => 'potentialAction',
    'label' => __( 'Potential Action', '__x__' ),
    'description' => __( 'Indicates a potential Action, which describes an idealized action in which this thing would play an "object" role.', '__x__' ),
    'schema_type' => 'Action',
    'type' => 'text',
  ),
  73 =>
  array (
    'id' => '_snippet_localbusiness_same_as',
    'name' => 'sameAs',
    'label' => __( 'Same As', '__x__' ),
    'description' => __( 'URL of a reference Web page that unambiguously indicates the item"s identity. E.g. the URL of the item"s Wikipedia page, Freebase page, or official website.', '__x__' ),
    'schema_type' => 'URL',
    'type' => 'text',
  ),
  74 =>
  array (
    'id' => '_snippet_localbusiness_url',
    'name' => 'url',
    'label' => __( 'Url', '__x__' ),
    'description' => __( 'URL of the item.', '__x__' ),
    'schema_type' => 'URL',
    'type' => 'text',
  ),
);
