<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-PERSON.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of Person
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
    'id' => '_snippet_person_type',
    'name' => '@type',
    'label' => __( 'Type', '__x__' ),
    'description' => __( 'Type of schema', '__x__' ),
    'schema_type' => '',
    'type' => 'type',
  ),
  array (
    'id' => '_snippet_person_name',
    'name' => 'name',
    'label' => __( 'Name', '__x__' ),
    'description' => __( 'The name of the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_person_alternate_name',
    'name' => 'alternateName',
    'label' => __( 'Nickname', '__x__' ),
    'description' => __( 'An alias for the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_person_image',
    'name' => 'image',
    'label' => __( 'Image', '__x__' ),
    'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'media',
  ),
  array (
    'id' => '_snippet_person_url',
    'name' => 'url',
    'label' => __( 'Url', '__x__' ),
    'description' => __( 'URL of the item.', '__x__' ),
    'schema_type' => 'URL',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_person_job_title',
    'name' => 'jobTitle',
    'label' => __( 'Job Title', '__x__' ),
    'description' => __( 'The job title of the person (for example, Financial Manager).', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_person_address',
    'name' => 'address',
    'label' => __( 'Address', '__x__' ),
    'description' => __( 'Physical address of the item.', '__x__' ),
    'schema_type' => 'PostalAddress',
    'type' => 'postaladdress',
  ),


  //
  // 1 =>
  // array (
  //   'id' => '_snippet_person_additional_name',
  //   'name' => 'additionalName',
  //   'label' => __( 'Additional Name', '__x__' ),
  //   'description' => __( 'An additional name for a Person, can be used for a middle name.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 2 =>
  // 3 =>
  // array (
  //   'id' => '_snippet_person_affiliation',
  //   'name' => 'affiliation',
  //   'label' => __( 'Affiliation', '__x__' ),
  //   'description' => __( 'An organization that this person is affiliated with. For example, a school/university, a club, or a team.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 4 =>
  // array (
  //   'id' => '_snippet_person_alumni_of',
  //   'name' => 'alumniOf',
  //   'label' => __( 'Alumni Of', '__x__' ),
  //   'description' => __( 'An organization that the person is an alumni of. Inverse property: alumni.', '__x__' ),
  //   'schema_type' => 'EducationalOrganization',
  //   'type' => 'text',
  // ),
  // 5 =>
  // array (
  //   'id' => '_snippet_person_award',
  //   'name' => 'award',
  //   'label' => __( 'Award', '__x__' ),
  //   'description' => __( 'An award won by or for this item. Supersedes awards.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 6 =>
  // array (
  //   'id' => '_snippet_person_birth_date',
  //   'name' => 'birthDate',
  //   'label' => __( 'Birth Date', '__x__' ),
  //   'description' => __( 'Date of birth.', '__x__' ),
  //   'schema_type' => 'Date',
  //   'type' => 'text',
  // ),
  // 7 =>
  // array (
  //   'id' => '_snippet_person_birth_place',
  //   'name' => 'birthPlace',
  //   'label' => __( 'Birth Place', '__x__' ),
  //   'description' => __( 'The place where the person was born.', '__x__' ),
  //   'schema_type' => 'Place',
  //   'type' => 'text',
  // ),
  // 8 =>
  // array (
  //   'id' => '_snippet_person_brand',
  //   'name' => 'brand',
  //   'label' => __( 'Brand', '__x__' ),
  //   'description' => __( 'The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.', '__x__' ),
  //   'schema_type' => 'Brand',
  //   'type' => 'text',
  // ),
  // 9 =>
  // array (
  //   'id' => '_snippet_person_children',
  //   'name' => 'children',
  //   'label' => __( 'Children', '__x__' ),
  //   'description' => __( 'A child of the person.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 10 =>
  // array (
  //   'id' => '_snippet_person_colleague',
  //   'name' => 'colleague',
  //   'label' => __( 'Colleague', '__x__' ),
  //   'description' => __( 'A colleague of the person. Supersedes colleagues.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 11 =>
  // array (
  //   'id' => '_snippet_person_contact_point',
  //   'name' => 'contactPoint',
  //   'label' => __( 'Contact Point', '__x__' ),
  //   'description' => __( 'A contact point for a person or organization. Supersedes contactPoints.', '__x__' ),
  //   'schema_type' => 'ContactPoint',
  //   'type' => 'text',
  // ),
  // 12 =>
  // array (
  //   'id' => '_snippet_person_death_date',
  //   'name' => 'deathDate',
  //   'label' => __( 'Death Date', '__x__' ),
  //   'description' => __( 'Date of death.', '__x__' ),
  //   'schema_type' => 'Date',
  //   'type' => 'text',
  // ),
  // 13 =>
  // array (
  //   'id' => '_snippet_person_death_place',
  //   'name' => 'deathPlace',
  //   'label' => __( 'Death Place', '__x__' ),
  //   'description' => __( 'The place where the person died.', '__x__' ),
  //   'schema_type' => 'Place',
  //   'type' => 'text',
  // ),
  // 14 =>
  // array (
  //   'id' => '_snippet_person_duns',
  //   'name' => 'duns',
  //   'label' => __( 'Duns', '__x__' ),
  //   'description' => __( 'The Dun &amp; Bradstreet DUNS number for identifying an organization or business person.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 15 =>
  // array (
  //   'id' => '_snippet_person_email',
  //   'name' => 'email',
  //   'label' => __( 'Email', '__x__' ),
  //   'description' => __( 'Email address.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 16 =>
  // array (
  //   'id' => '_snippet_person_family_name',
  //   'name' => 'familyName',
  //   'label' => __( 'Family Name', '__x__' ),
  //   'description' => __( 'Family name. In the U.S., the last name of an Person. This can be used along with givenName instead of the name property.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 17 =>
  // array (
  //   'id' => '_snippet_person_fax_number',
  //   'name' => 'faxNumber',
  //   'label' => __( 'Fax Number', '__x__' ),
  //   'description' => __( 'The fax number.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 18 =>
  // array (
  //   'id' => '_snippet_person_follows',
  //   'name' => 'follows',
  //   'label' => __( 'Follows', '__x__' ),
  //   'description' => __( 'The most generic uni-directional social relation.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 19 =>
  // array (
  //   'id' => '_snippet_person_funder',
  //   'name' => 'funder',
  //   'label' => __( 'Funder', '__x__' ),
  //   'description' => __( 'A person or organization that supports (sponsors) something through some kind of financial contribution.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 20 =>
  // array (
  //   'id' => '_snippet_person_gender',
  //   'name' => 'gender',
  //   'label' => __( 'Gender', '__x__' ),
  //   'description' => __( 'Gender of the person. While http://schema.org/Male and http://schema.org/Female may be used, text strings are also acceptable for people who do not identify as a binary gender.', '__x__' ),
  //   'schema_type' => 'GenderType',
  //   'type' => 'text',
  // ),
  // 21 =>
  // array (
  //   'id' => '_snippet_person_given_name',
  //   'name' => 'givenName',
  //   'label' => __( 'Given Name', '__x__' ),
  //   'description' => __( 'Given name. In the U.S., the first name of a Person. This can be used along with familyName instead of the name property.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 22 =>
  // array (
  //   'id' => '_snippet_person_global_location_number',
  //   'name' => 'globalLocationNumber',
  //   'label' => __( 'Global Location Number', '__x__' ),
  //   'description' => __( 'The Global Location Number (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place. The GLN is a 13-digit number used to identify parties and physical locations.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 23 =>
  // array (
  //   'id' => '_snippet_person_has_offer_catalog',
  //   'name' => 'hasOfferCatalog',
  //   'label' => __( 'Has Offer Catalog', '__x__' ),
  //   'description' => __( 'Indicates an OfferCatalog listing for this Organization, Person, or Service.', '__x__' ),
  //   'schema_type' => 'OfferCatalog',
  //   'type' => 'text',
  // ),
  // 24 =>
  // array (
  //   'id' => '_snippet_person_has_pos',
  //   'name' => 'hasPOS',
  //   'label' => __( 'Has POS', '__x__' ),
  //   'description' => __( 'Points-of-Sales operated by the organization or person.', '__x__' ),
  //   'schema_type' => 'Place',
  //   'type' => 'text',
  // ),
  // 25 =>
  // array (
  //   'id' => '_snippet_person_height',
  //   'name' => 'height',
  //   'label' => __( 'Height', '__x__' ),
  //   'description' => __( 'The height of the item.', '__x__' ),
  //   'schema_type' => 'Distance',
  //   'type' => 'text',
  // ),
  // 26 =>
  // array (
  //   'id' => '_snippet_person_home_location',
  //   'name' => 'homeLocation',
  //   'label' => __( 'Home Location', '__x__' ),
  //   'description' => __( 'A contact location for a person"s residence.', '__x__' ),
  //   'schema_type' => 'ContactPoint',
  //   'type' => 'text',
  // ),
  // 27 =>
  // array (
  //   'id' => '_snippet_person_honorific_prefix',
  //   'name' => 'honorificPrefix',
  //   'label' => __( 'Honorific Prefix', '__x__' ),
  //   'description' => __( 'An honorific prefix preceding a Person"s name such as Dr/Mrs/Mr.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 28 =>
  // array (
  //   'id' => '_snippet_person_honorific_suffix',
  //   'name' => 'honorificSuffix',
  //   'label' => __( 'Honorific Suffix', '__x__' ),
  //   'description' => __( 'An honorific suffix preceding a Person"s name such as M.D. /PhD/MSCSW.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 29 =>
  // array (
  //   'id' => '_snippet_person_isic_v4',
  //   'name' => 'isicV4',
  //   'label' => __( 'Isic V4', '__x__' ),
  //   'description' => __( 'The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 30 =>
  // 31 =>
  // array (
  //   'id' => '_snippet_person_knows',
  //   'name' => 'knows',
  //   'label' => __( 'Knows', '__x__' ),
  //   'description' => __( 'The most generic bi-directional social/work relation.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 32 =>
  // array (
  //   'id' => '_snippet_person_makes_offer',
  //   'name' => 'makesOffer',
  //   'label' => __( 'Makes Offer', '__x__' ),
  //   'description' => __( 'A pointer to products or services offered by the organization or person. Inverse property: offeredBy.', '__x__' ),
  //   'schema_type' => 'Offer',
  //   'type' => 'text',
  // ),
  // 33 =>
  // array (
  //   'id' => '_snippet_person_member_of',
  //   'name' => 'memberOf',
  //   'label' => __( 'Member Of', '__x__' ),
  //   'description' => __( 'An Organization (or ProgramMembership) to which this Person or Organization belongs. Inverse property: member.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 34 =>
  // array (
  //   'id' => '_snippet_person_naics',
  //   'name' => 'naics',
  //   'label' => __( 'Naics', '__x__' ),
  //   'description' => __( 'The North American Industry Classification System (NAICS) code for a particular organization or business person.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 35 =>
  // array (
  //   'id' => '_snippet_person_nationality',
  //   'name' => 'nationality',
  //   'label' => __( 'Nationality', '__x__' ),
  //   'description' => __( 'Nationality of the person.', '__x__' ),
  //   'schema_type' => 'Country',
  //   'type' => 'text',
  // ),
  // 36 =>
  // array (
  //   'id' => '_snippet_person_net_worth',
  //   'name' => 'netWorth',
  //   'label' => __( 'Net Worth', '__x__' ),
  //   'description' => __( 'The total financial value of the person as calculated by subtracting assets from liabilities.', '__x__' ),
  //   'schema_type' => 'MonetaryAmount',
  //   'type' => 'text',
  // ),
  // 37 =>
  // array (
  //   'id' => '_snippet_person_owns',
  //   'name' => 'owns',
  //   'label' => __( 'Owns', '__x__' ),
  //   'description' => __( 'Products owned by the organization or person.', '__x__' ),
  //   'schema_type' => 'OwnershipInfo',
  //   'type' => 'text',
  // ),
  // 38 =>
  // array (
  //   'id' => '_snippet_person_parent',
  //   'name' => 'parent',
  //   'label' => __( 'Parent', '__x__' ),
  //   'description' => __( 'A parent of this person. Supersedes parents.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 39 =>
  // array (
  //   'id' => '_snippet_person_performer_in',
  //   'name' => 'performerIn',
  //   'label' => __( 'Performer In', '__x__' ),
  //   'description' => __( 'Event that this person is a performer or participant in.', '__x__' ),
  //   'schema_type' => 'Event',
  //   'type' => 'text',
  // ),
  // 40 =>
  // array (
  //   'id' => '_snippet_person_related_to',
  //   'name' => 'relatedTo',
  //   'label' => __( 'Related To', '__x__' ),
  //   'description' => __( 'The most generic familial relation.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 41 =>
  // array (
  //   'id' => '_snippet_person_seeks',
  //   'name' => 'seeks',
  //   'label' => __( 'Seeks', '__x__' ),
  //   'description' => __( 'A pointer to products or services sought by the organization or person (demand).', '__x__' ),
  //   'schema_type' => 'Demand',
  //   'type' => 'text',
  // ),
  // 42 =>
  // array (
  //   'id' => '_snippet_person_sibling',
  //   'name' => 'sibling',
  //   'label' => __( 'Sibling', '__x__' ),
  //   'description' => __( 'A sibling of the person. Supersedes siblings.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 43 =>
  // array (
  //   'id' => '_snippet_person_sponsor',
  //   'name' => 'sponsor',
  //   'label' => __( 'Sponsor', '__x__' ),
  //   'description' => __( 'A person or organization that supports a thing through a pledge, promise, or financial contribution. e.g. a sponsor of a Medical Study or a corporate sponsor of an event.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 44 =>
  // array (
  //   'id' => '_snippet_person_spouse',
  //   'name' => 'spouse',
  //   'label' => __( 'Spouse', '__x__' ),
  //   'description' => __( 'The person"s spouse.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 45 =>
  // array (
  //   'id' => '_snippet_person_tax_id',
  //   'name' => 'taxID',
  //   'label' => __( 'Tax ID', '__x__' ),
  //   'description' => __( 'The Tax / Fiscal ID of the organization or person, e.g. the TIN in the US or the CIF/NIF in Spain.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 46 =>
  // array (
  //   'id' => '_snippet_person_telephone',
  //   'name' => 'telephone',
  //   'label' => __( 'Telephone', '__x__' ),
  //   'description' => __( 'The telephone number.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 47 =>
  // array (
  //   'id' => '_snippet_person_vat_id',
  //   'name' => 'vatID',
  //   'label' => __( 'Vat ID', '__x__' ),
  //   'description' => __( 'The Value-added Tax ID of the organization or person.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 48 =>
  // array (
  //   'id' => '_snippet_person_weight',
  //   'name' => 'weight',
  //   'label' => __( 'Weight', '__x__' ),
  //   'description' => __( 'The weight of the product or person.', '__x__' ),
  //   'schema_type' => 'QuantitativeValue',
  //   'type' => 'text',
  // ),
  // 49 =>
  // array (
  //   'id' => '_snippet_person_work_location',
  //   'name' => 'workLocation',
  //   'label' => __( 'Work Location', '__x__' ),
  //   'description' => __( 'A contact location for a person"s place of work.', '__x__' ),
  //   'schema_type' => 'ContactPoint',
  //   'type' => 'text',
  // ),
  // 50 =>
  // array (
  //   'id' => '_snippet_person_works_for',
  //   'name' => 'worksFor',
  //   'label' => __( 'Works For', '__x__' ),
  //   'description' => __( 'Organizations that the person works for.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 51 =>
  // array (
  //   'id' => '_snippet_person_additional_type',
  //   'name' => 'additionalType',
  //   'label' => __( 'Additional Type', '__x__' ),
  //   'description' => __( 'An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax. This is a relationship between something and a class that the thing is in. In RDFa syntax, it is better to use the native RDFa syntax - the "typeof" attribute - for multiple types. Schema.org tools may have only weaker understanding of extra types, in particular those defined externally.', '__x__' ),
  //   'schema_type' => 'URL',
  //   'type' => 'text',
  // ),
  // 52 =>
  // 53 =>
  // array (
  //   'id' => '_snippet_person_description',
  //   'name' => 'description',
  //   'label' => __( 'Description', '__x__' ),
  //   'description' => __( 'A description of the item.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 54 =>
  // array (
  //   'id' => '_snippet_person_disambiguating_description',
  //   'name' => 'disambiguatingDescription',
  //   'label' => __( 'Disambiguating Description', '__x__' ),
  //   'description' => __( 'A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.', '__x__' ),
  //   'schema_type' => 'Text',
  //   'type' => 'text',
  // ),
  // 55 =>
  // 56 =>
  // array (
  //   'id' => '_snippet_person_main_entity_of_page',
  //   'name' => 'mainEntityOfPage',
  //   'label' => __( 'Main Entity Of Page', '__x__' ),
  //   'description' => __( 'Indicates a page (or other CreativeWork) for which this thing is the main entity being described. See background notes for details. Inverse property: mainEntity.', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 57 =>
  // 58 =>
  // array (
  //   'id' => '_snippet_person_potential_action',
  //   'name' => 'potentialAction',
  //   'label' => __( 'Potential Action', '__x__' ),
  //   'description' => __( 'Indicates a potential Action, which describes an idealized action in which this thing would play an "object" role.', '__x__' ),
  //   'schema_type' => 'Action',
  //   'type' => 'text',
  // ),
  // 59 =>
  // array (
  //   'id' => '_snippet_person_same_as',
  //   'name' => 'sameAs',
  //   'label' => __( 'Same As', '__x__' ),
  //   'description' => __( 'URL of a reference Web page that unambiguously indicates the item"s identity. E.g. the URL of the item"s Wikipedia page, Freebase page, or official website.', '__x__' ),
  //   'schema_type' => 'URL',
  //   'type' => 'text',
  // ),
  // 60 =>
  // 61 =>
  // array (
  //   'id' => '_snippet_person_accountable_person',
  //   'name' => 'accountablePerson',
  //   'label' => __( 'Accountable Person', '__x__' ),
  //   'description' => __( 'Specifies the Person that is legally accountable for the CreativeWork. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 62 =>
  // array (
  //   'id' => '_snippet_person_acquired_from',
  //   'name' => 'acquiredFrom',
  //   'label' => __( 'Acquired From', '__x__' ),
  //   'description' => __( 'The organization or person from which the product was acquired. ', '__x__' ),
  //   'schema_type' => 'OwnershipInfo',
  //   'type' => 'text',
  // ),
  // 63 =>
  // array (
  //   'id' => '_snippet_person_actor',
  //   'name' => 'actor',
  //   'label' => __( 'Actor', '__x__' ),
  //   'description' => __( 'An actor, e.g. in tv, radio, movie, video games etc., or in an event. Actors can be associated with individual items or with a series, episode, clip. Supersedes actors.', '__x__' ),
  //   'schema_type' => 'Clip',
  //   'type' => 'text',
  // ),
  // 64 =>
  // array (
  //   'id' => '_snippet_person_agent',
  //   'name' => 'agent',
  //   'label' => __( 'Agent', '__x__' ),
  //   'description' => __( 'The direct performer or driver of the action (animate or inanimate). e.g. John wrote a book. ', '__x__' ),
  //   'schema_type' => 'Action',
  //   'type' => 'text',
  // ),
  // 65 =>
  // array (
  //   'id' => '_snippet_person_alumni',
  //   'name' => 'alumni',
  //   'label' => __( 'Alumni', '__x__' ),
  //   'description' => __( 'Alumni of an organization.  inverse property: alumniOf.', '__x__' ),
  //   'schema_type' => 'EducationalOrganization',
  //   'type' => 'text',
  // ),
  // 66 =>
  // array (
  //   'id' => '_snippet_person_artist',
  //   'name' => 'artist',
  //   'label' => __( 'Artist', '__x__' ),
  //   'description' => __( 'The primary artist for a work in a medium other than pencils or digital line art--for example, if the primary artwork is done in watercolors or digital paints. ', '__x__' ),
  //   'schema_type' => 'ComicIssue',
  //   'type' => 'text',
  // ),
  // 67 =>
  // array (
  //   'id' => '_snippet_person_athlete',
  //   'name' => 'athlete',
  //   'label' => __( 'Athlete', '__x__' ),
  //   'description' => __( 'A person that acts as performing member of a sports team; a player as opposed to a coach. ', '__x__' ),
  //   'schema_type' => 'SportsTeam',
  //   'type' => 'text',
  // ),
  // 68 =>
  // array (
  //   'id' => '_snippet_person_attendee',
  //   'name' => 'attendee',
  //   'label' => __( 'Attendee', '__x__' ),
  //   'description' => __( 'A person or organization attending the event. Supersedes attendees.', '__x__' ),
  //   'schema_type' => 'Event',
  //   'type' => 'text',
  // ),
  // 69 =>
  // array (
  //   'id' => '_snippet_person_author',
  //   'name' => 'author',
  //   'label' => __( 'Author', '__x__' ),
  //   'description' => __( 'The author of this content or rating. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag. That is equivalent to this and may be used interchangeably. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 70 =>
  // array (
  //   'id' => '_snippet_person_away_team',
  //   'name' => 'awayTeam',
  //   'label' => __( 'Away Team', '__x__' ),
  //   'description' => __( 'The away team in a sports event. ', '__x__' ),
  //   'schema_type' => 'SportsEvent',
  //   'type' => 'text',
  // ),
  // 71 =>
  // array (
  //   'id' => '_snippet_person_borrower',
  //   'name' => 'borrower',
  //   'label' => __( 'Borrower', '__x__' ),
  //   'description' => __( 'A sub property of participant. The person that borrows the object being lent. ', '__x__' ),
  //   'schema_type' => 'LendAction',
  //   'type' => 'text',
  // ),
  // 72 =>
  // array (
  //   'id' => '_snippet_person_broker',
  //   'name' => 'broker',
  //   'label' => __( 'Broker', '__x__' ),
  //   'description' => __( 'An entity that arranges for an exchange between a buyer and a seller. In most cases a broker never acquires or releases ownership of a product or service involved in an exchange. If it is not clear whether an entity is a broker, seller, or buyer, the latter two terms are preferred. Supersedes bookingAgent.', '__x__' ),
  //   'schema_type' => 'Invoice',
  //   'type' => 'text',
  // ),
  // 73 =>
  // array (
  //   'id' => '_snippet_person_buyer',
  //   'name' => 'buyer',
  //   'label' => __( 'Buyer', '__x__' ),
  //   'description' => __( 'A sub property of participant. The participant/person/organization that bought the object. ', '__x__' ),
  //   'schema_type' => 'SellAction',
  //   'type' => 'text',
  // ),
  // 74 =>
  // array (
  //   'id' => '_snippet_person_candidate',
  //   'name' => 'candidate',
  //   'label' => __( 'Candidate', '__x__' ),
  //   'description' => __( 'A sub property of object. The candidate subject of this action. ', '__x__' ),
  //   'schema_type' => 'VoteAction',
  //   'type' => 'text',
  // ),
  // 75 =>
  // array (
  //   'id' => '_snippet_person_character',
  //   'name' => 'character',
  //   'label' => __( 'Character', '__x__' ),
  //   'description' => __( 'Fictional person connected with a creative work. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 76 =>
  // array (
  //   'id' => '_snippet_person_children',
  //   'name' => 'children',
  //   'label' => __( 'Children', '__x__' ),
  //   'description' => __( 'A child of the person. ', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 77 =>
  // array (
  //   'id' => '_snippet_person_coach',
  //   'name' => 'coach',
  //   'label' => __( 'Coach', '__x__' ),
  //   'description' => __( 'A person that acts in a coaching role for a sports team. ', '__x__' ),
  //   'schema_type' => 'SportsTeam',
  //   'type' => 'text',
  // ),
  // 78 =>
  // array (
  //   'id' => '_snippet_person_colleague',
  //   'name' => 'colleague',
  //   'label' => __( 'Colleague', '__x__' ),
  //   'description' => __( 'A colleague of the person. Supersedes colleagues.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 79 =>
  // array (
  //   'id' => '_snippet_person_colorist',
  //   'name' => 'colorist',
  //   'label' => __( 'Colorist', '__x__' ),
  //   'description' => __( 'The individual who adds color to inked drawings. ', '__x__' ),
  //   'schema_type' => 'ComicIssue',
  //   'type' => 'text',
  // ),
  // 80 =>
  // array (
  //   'id' => '_snippet_person_competitor',
  //   'name' => 'competitor',
  //   'label' => __( 'Competitor', '__x__' ),
  //   'description' => __( 'A competitor in a sports event. ', '__x__' ),
  //   'schema_type' => 'SportsEvent',
  //   'type' => 'text',
  // ),
  // 81 =>
  // array (
  //   'id' => '_snippet_person_composer',
  //   'name' => 'composer',
  //   'label' => __( 'Composer', '__x__' ),
  //   'description' => __( 'The person or organization who wrote a composition, or who is the composer of a work performed at some event. ', '__x__' ),
  //   'schema_type' => 'Event',
  //   'type' => 'text',
  // ),
  // 82 =>
  // array (
  //   'id' => '_snippet_person_contributor',
  //   'name' => 'contributor',
  //   'label' => __( 'Contributor', '__x__' ),
  //   'description' => __( 'A secondary contributor to the CreativeWork or Event. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 83 =>
  // array (
  //   'id' => '_snippet_person_copyright_holder',
  //   'name' => 'copyrightHolder',
  //   'label' => __( 'Copyright Holder', '__x__' ),
  //   'description' => __( 'The party holding the legal copyright to the CreativeWork. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 84 =>
  // array (
  //   'id' => '_snippet_person_creator',
  //   'name' => 'creator',
  //   'label' => __( 'Creator', '__x__' ),
  //   'description' => __( 'The creator/author of this CreativeWork. This is the same as the Author property for CreativeWork. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 85 =>
  // array (
  //   'id' => '_snippet_person_credited_to',
  //   'name' => 'creditedTo',
  //   'label' => __( 'Credited To', '__x__' ),
  //   'description' => __( 'The group the release is credited to if different than the byArtist. For example, Red and Blue is credited to "Stefani Germanotta Band", but by Lady Gaga. ', '__x__' ),
  //   'schema_type' => 'MusicRelease',
  //   'type' => 'text',
  // ),
  // 86 =>
  // array (
  //   'id' => '_snippet_person_customer',
  //   'name' => 'customer',
  //   'label' => __( 'Customer', '__x__' ),
  //   'description' => __( 'Party placing the order or paying the invoice. ', '__x__' ),
  //   'schema_type' => 'Invoice',
  //   'type' => 'text',
  // ),
  // 87 =>
  // array (
  //   'id' => '_snippet_person_director',
  //   'name' => 'director',
  //   'label' => __( 'Director', '__x__' ),
  //   'description' => __( 'A director of e.g. tv, radio, movie, video gaming etc. content, or of an event. Directors can be associated with individual items or with a series, episode, clip. Supersedes directors.', '__x__' ),
  //   'schema_type' => 'Clip',
  //   'type' => 'text',
  // ),
  // 88 =>
  // array (
  //   'id' => '_snippet_person_editor',
  //   'name' => 'editor',
  //   'label' => __( 'Editor', '__x__' ),
  //   'description' => __( 'Specifies the Person who edited the CreativeWork. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 89 =>
  // array (
  //   'id' => '_snippet_person_employee',
  //   'name' => 'employee',
  //   'label' => __( 'Employee', '__x__' ),
  //   'description' => __( 'Someone working for this organization. Supersedes employees.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 90 =>
  // array (
  //   'id' => '_snippet_person_endorsee',
  //   'name' => 'endorsee',
  //   'label' => __( 'Endorsee', '__x__' ),
  //   'description' => __( 'A sub property of participant. The person/organization being supported. ', '__x__' ),
  //   'schema_type' => 'EndorseAction',
  //   'type' => 'text',
  // ),
  // 91 =>
  // array (
  //   'id' => '_snippet_person_endorsers',
  //   'name' => 'endorsers',
  //   'label' => __( 'Endorsers', '__x__' ),
  //   'description' => __( 'People or organizations that endorse the plan. ', '__x__' ),
  //   'schema_type' => 'Diet',
  //   'type' => 'text',
  // ),
  // 92 =>
  // array (
  //   'id' => '_snippet_person_followee',
  //   'name' => 'followee',
  //   'label' => __( 'Followee', '__x__' ),
  //   'description' => __( 'A sub property of object. The person or organization being followed. ', '__x__' ),
  //   'schema_type' => 'FollowAction',
  //   'type' => 'text',
  // ),
  // 93 =>
  // array (
  //   'id' => '_snippet_person_follows',
  //   'name' => 'follows',
  //   'label' => __( 'Follows', '__x__' ),
  //   'description' => __( 'The most generic uni-directional social relation. ', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 94 =>
  // array (
  //   'id' => '_snippet_person_founder',
  //   'name' => 'founder',
  //   'label' => __( 'Founder', '__x__' ),
  //   'description' => __( 'A person who founded this organization. Supersedes founders.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 95 =>
  // array (
  //   'id' => '_snippet_person_funder',
  //   'name' => 'funder',
  //   'label' => __( 'Funder', '__x__' ),
  //   'description' => __( 'A person or organization that supports (sponsors) something through some kind of financial contribution. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 96 =>
  // array (
  //   'id' => '_snippet_person_grantee',
  //   'name' => 'grantee',
  //   'label' => __( 'Grantee', '__x__' ),
  //   'description' => __( 'The person, organization, contact point, or audience that has been granted this permission. ', '__x__' ),
  //   'schema_type' => 'DigitalDocumentPermission',
  //   'type' => 'text',
  // ),
  // 97 =>
  // array (
  //   'id' => '_snippet_person_home_team',
  //   'name' => 'homeTeam',
  //   'label' => __( 'Home Team', '__x__' ),
  //   'description' => __( 'The home team in a sports event. ', '__x__' ),
  //   'schema_type' => 'SportsEvent',
  //   'type' => 'text',
  // ),
  // 98 =>
  // array (
  //   'id' => '_snippet_person_illustrator',
  //   'name' => 'illustrator',
  //   'label' => __( 'Illustrator', '__x__' ),
  //   'description' => __( 'The illustrator of the book. ', '__x__' ),
  //   'schema_type' => 'Book',
  //   'type' => 'text',
  // ),
  // 99 =>
  // array (
  //   'id' => '_snippet_person_inker',
  //   'name' => 'inker',
  //   'label' => __( 'Inker', '__x__' ),
  //   'description' => __( 'The individual who traces over the pencil drawings in ink after pencils are complete. ', '__x__' ),
  //   'schema_type' => 'ComicIssue',
  //   'type' => 'text',
  // ),
  // 100 =>
  // array (
  //   'id' => '_snippet_person_instructor',
  //   'name' => 'instructor',
  //   'label' => __( 'Instructor', '__x__' ),
  //   'description' => __( 'A person assigned to instruct or provide instructional assistance for the CourseInstance. ', '__x__' ),
  //   'schema_type' => 'CourseInstance',
  //   'type' => 'text',
  // ),
  // 101 =>
  // array (
  //   'id' => '_snippet_person_knows',
  //   'name' => 'knows',
  //   'label' => __( 'Knows', '__x__' ),
  //   'description' => __( 'The most generic bi-directional social/work relation. ', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 102 =>
  // array (
  //   'id' => '_snippet_person_landlord',
  //   'name' => 'landlord',
  //   'label' => __( 'Landlord', '__x__' ),
  //   'description' => __( 'A sub property of participant. The owner of the real estate property. ', '__x__' ),
  //   'schema_type' => 'RentAction',
  //   'type' => 'text',
  // ),
  // 103 =>
  // array (
  //   'id' => '_snippet_person_lender',
  //   'name' => 'lender',
  //   'label' => __( 'Lender', '__x__' ),
  //   'description' => __( 'A sub property of participant. The person that lends the object being borrowed. ', '__x__' ),
  //   'schema_type' => 'BorrowAction',
  //   'type' => 'text',
  // ),
  // 104 =>
  // array (
  //   'id' => '_snippet_person_letterer',
  //   'name' => 'letterer',
  //   'label' => __( 'Letterer', '__x__' ),
  //   'description' => __( 'The individual who adds lettering, including speech balloons and sound effects, to artwork. ', '__x__' ),
  //   'schema_type' => 'ComicIssue',
  //   'type' => 'text',
  // ),
  // 105 =>
  // array (
  //   'id' => '_snippet_person_loser',
  //   'name' => 'loser',
  //   'label' => __( 'Loser', '__x__' ),
  //   'description' => __( 'A sub property of participant. The loser of the action. ', '__x__' ),
  //   'schema_type' => 'WinAction',
  //   'type' => 'text',
  // ),
  // 106 =>
  // array (
  //   'id' => '_snippet_person_lyricist',
  //   'name' => 'lyricist',
  //   'label' => __( 'Lyricist', '__x__' ),
  //   'description' => __( 'The person who wrote the words. ', '__x__' ),
  //   'schema_type' => 'MusicComposition',
  //   'type' => 'text',
  // ),
  // 107 =>
  // array (
  //   'id' => '_snippet_person_member',
  //   'name' => 'member',
  //   'label' => __( 'Member', '__x__' ),
  //   'description' => __( 'A member of an Organization or a ProgramMembership. Organizations can be members of organizations; ProgramMembership is typically for individuals. Supersedes members. inverse property: memberOf.', '__x__' ),
  //   'schema_type' => 'Organization',
  //   'type' => 'text',
  // ),
  // 108 =>
  // array (
  //   'id' => '_snippet_person_music_by',
  //   'name' => 'musicBy',
  //   'label' => __( 'Music By', '__x__' ),
  //   'description' => __( 'The composer of the soundtrack. ', '__x__' ),
  //   'schema_type' => 'Clip',
  //   'type' => 'text',
  // ),
  // 109 =>
  // array (
  //   'id' => '_snippet_person_offered_by',
  //   'name' => 'offeredBy',
  //   'label' => __( 'Offered By', '__x__' ),
  //   'description' => __( 'A pointer to the organization or person making the offer.  inverse property: makesOffer.', '__x__' ),
  //   'schema_type' => 'Offer',
  //   'type' => 'text',
  // ),
  // 110 =>
  // array (
  //   'id' => '_snippet_person_opponent',
  //   'name' => 'opponent',
  //   'label' => __( 'Opponent', '__x__' ),
  //   'description' => __( 'A sub property of participant. The opponent on this action. ', '__x__' ),
  //   'schema_type' => 'ExerciseAction',
  //   'type' => 'text',
  // ),
  // 111 =>
  // array (
  //   'id' => '_snippet_person_organizer',
  //   'name' => 'organizer',
  //   'label' => __( 'Organizer', '__x__' ),
  //   'description' => __( 'An organizer of an Event. ', '__x__' ),
  //   'schema_type' => 'Event',
  //   'type' => 'text',
  // ),
  // 112 =>
  // array (
  //   'id' => '_snippet_person_parent',
  //   'name' => 'parent',
  //   'label' => __( 'Parent', '__x__' ),
  //   'description' => __( 'A parent of this person. Supersedes parents.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 113 =>
  // array (
  //   'id' => '_snippet_person_participant',
  //   'name' => 'participant',
  //   'label' => __( 'Participant', '__x__' ),
  //   'description' => __( 'Other co-agents that participated in the action indirectly. e.g. John wrote a book with Steve. ', '__x__' ),
  //   'schema_type' => 'Action',
  //   'type' => 'text',
  // ),
  // 114 =>
  // array (
  //   'id' => '_snippet_person_penciler',
  //   'name' => 'penciler',
  //   'label' => __( 'Penciler', '__x__' ),
  //   'description' => __( 'The individual who draws the primary narrative artwork. ', '__x__' ),
  //   'schema_type' => 'ComicIssue',
  //   'type' => 'text',
  // ),
  // 115 =>
  // array (
  //   'id' => '_snippet_person_performer',
  //   'name' => 'performer',
  //   'label' => __( 'Performer', '__x__' ),
  //   'description' => __( 'A performer at the event&#x2014;for example, a presenter, musician, musical group or actor. Supersedes performers.', '__x__' ),
  //   'schema_type' => 'Event',
  //   'type' => 'text',
  // ),
  // 116 =>
  // array (
  //   'id' => '_snippet_person_producer',
  //   'name' => 'producer',
  //   'label' => __( 'Producer', '__x__' ),
  //   'description' => __( 'The person or organization who produced the work (e.g. music album, movie, tv/radio series etc.). ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 117 =>
  // array (
  //   'id' => '_snippet_person_provider',
  //   'name' => 'provider',
  //   'label' => __( 'Provider', '__x__' ),
  //   'description' => __( 'The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider. A provider may also serve as the seller. Supersedes carrier.', '__x__' ),
  //   'schema_type' => 'BusTrip',
  //   'type' => 'text',
  // ),
  // 118 =>
  // array (
  //   'id' => '_snippet_person_published_by',
  //   'name' => 'publishedBy',
  //   'label' => __( 'Published By', '__x__' ),
  //   'description' => __( 'An agent associated with the publication event. ', '__x__' ),
  //   'schema_type' => 'PublicationEvent',
  //   'type' => 'text',
  // ),
  // 119 =>
  // array (
  //   'id' => '_snippet_person_publisher',
  //   'name' => 'publisher',
  //   'label' => __( 'Publisher', '__x__' ),
  //   'description' => __( 'The publisher of the creative work. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 120 =>
  // array (
  //   'id' => '_snippet_person_read_by',
  //   'name' => 'readBy',
  //   'label' => __( 'Read By', '__x__' ),
  //   'description' => __( 'A person who reads (performs) the audiobook. ', '__x__' ),
  //   'schema_type' => 'Audiobook',
  //   'type' => 'text',
  // ),
  // 121 =>
  // array (
  //   'id' => '_snippet_person_recipient',
  //   'name' => 'recipient',
  //   'label' => __( 'Recipient', '__x__' ),
  //   'description' => __( 'A sub property of participant. The participant who is at the receiving end of the action. ', '__x__' ),
  //   'schema_type' => 'AuthorizeAction',
  //   'type' => 'text',
  // ),
  // 122 =>
  // array (
  //   'id' => '_snippet_person_related_to',
  //   'name' => 'relatedTo',
  //   'label' => __( 'Related To', '__x__' ),
  //   'description' => __( 'The most generic familial relation. ', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 123 =>
  // array (
  //   'id' => '_snippet_person_reviewed_by',
  //   'name' => 'reviewedBy',
  //   'label' => __( 'Reviewed By', '__x__' ),
  //   'description' => __( 'People or organizations that have reviewed the content on this web page for accuracy and/or completeness. ', '__x__' ),
  //   'schema_type' => 'WebPage',
  //   'type' => 'text',
  // ),
  // 124 =>
  // array (
  //   'id' => '_snippet_person_seller',
  //   'name' => 'seller',
  //   'label' => __( 'Seller', '__x__' ),
  //   'description' => __( 'An entity which offers (sells / leases / lends / loans) the services / goods. A seller may also be a provider. Supersedes merchant.', '__x__' ),
  //   'schema_type' => 'BuyAction',
  //   'type' => 'text',
  // ),
  // 125 =>
  // array (
  //   'id' => '_snippet_person_sender',
  //   'name' => 'sender',
  //   'label' => __( 'Sender', '__x__' ),
  //   'description' => __( 'A sub property of participant. The participant who is at the sending end of the action. ', '__x__' ),
  //   'schema_type' => 'Message',
  //   'type' => 'text',
  // ),
  // 126 =>
  // array (
  //   'id' => '_snippet_person_sibling',
  //   'name' => 'sibling',
  //   'label' => __( 'Sibling', '__x__' ),
  //   'description' => __( 'A sibling of the person. Supersedes siblings.', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 127 =>
  // array (
  //   'id' => '_snippet_person_spoken_by_character',
  //   'name' => 'spokenByCharacter',
  //   'label' => __( 'Spoken By Character', '__x__' ),
  //   'description' => __( 'The (e.g. fictional) character, Person or Organization to whom the quotation is attributed within the containing CreativeWork. ', '__x__' ),
  //   'schema_type' => 'Quotation',
  //   'type' => 'text',
  // ),
  // 128 =>
  // array (
  //   'id' => '_snippet_person_sponsor',
  //   'name' => 'sponsor',
  //   'label' => __( 'Sponsor', '__x__' ),
  //   'description' => __( 'A person or organization that supports a thing through a pledge, promise, or financial contribution. e.g. a sponsor of a Medical Study or a corporate sponsor of an event. ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 129 =>
  // array (
  //   'id' => '_snippet_person_spouse',
  //   'name' => 'spouse',
  //   'label' => __( 'Spouse', '__x__' ),
  //   'description' => __( 'The person"s spouse. ', '__x__' ),
  //   'schema_type' => 'Person',
  //   'type' => 'text',
  // ),
  // 130 =>
  // array (
  //   'id' => '_snippet_person_translator',
  //   'name' => 'translator',
  //   'label' => __( 'Translator', '__x__' ),
  //   'description' => __( 'An agent responsible for rendering a translated work from a source work ', '__x__' ),
  //   'schema_type' => 'CreativeWork',
  //   'type' => 'text',
  // ),
  // 131 =>
  // array (
  //   'id' => '_snippet_person_under_name',
  //   'name' => 'underName',
  //   'label' => __( 'Under Name', '__x__' ),
  //   'description' => __( 'The person or organization the reservation or ticket is for. ', '__x__' ),
  //   'schema_type' => 'Reservation',
  //   'type' => 'text',
  // ),
  // 132 =>
  // array (
  //   'id' => '_snippet_person_winner',
  //   'name' => 'winner',
  //   'label' => __( 'Winner', '__x__' ),
  //   'description' => __( 'A sub property of participant. The winner of the action. ', '__x__' ),
  //   'schema_type' => 'LoseAction',
  //   'type' => 'text',
  // ),
);
