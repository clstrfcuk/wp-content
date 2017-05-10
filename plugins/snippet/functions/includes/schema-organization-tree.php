<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-ORGANIZATION-TREE.PHP
// -----------------------------------------------------------------------------
// List of types of contact, used on Schema.org:[root]:@type
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Array of values
// =============================================================================

// Array of values
// =============================================================================

return array (
  'name' => 'Organization',
  'label' => 'Organization',
  'description' => 'An organization such as a school, NGO, corporation, club, etc.',
  'children' =>
  array (
    0 =>
    array (
      'name' => 'Airline',
      'label' => 'Airline',
      'description' => 'An organization that provides flights for passengers.',
      'children' =>
      array (
      ),
    ),
    1 =>
    array (
      'name' => 'Corporation',
      'label' => 'Corporation',
      'description' => 'Organization: A business corporation.',
      'children' =>
      array (
      ),
    ),
    2 =>
    array (
      'name' => 'EducationalOrganization',
      'label' => 'Educational Organization',
      'description' => 'An educational organization.',
      'children' =>
      array (
        0 =>
        array (
          'name' => 'CollegeOrUniversity',
          'label' => 'College Or University',
          'description' => 'A college, university, or other third-level educational institution.',
        ),
        1 =>
        array (
          'name' => 'ElementarySchool',
          'label' => 'Elementary School',
          'description' => 'An elementary school.',
        ),
        2 =>
        array (
          'name' => 'HighSchool',
          'label' => 'High School',
          'description' => 'A high school.',
        ),
        3 =>
        array (
          'name' => 'MiddleSchool',
          'label' => 'Middle School',
          'description' => 'A middle school (typically for children aged around 11-14, although this varies somewhat).',
        ),
        4 =>
        array (
          'name' => 'Preschool',
          'label' => 'Preschool',
          'description' => 'A preschool.',
        ),
        5 =>
        array (
          'name' => 'School',
          'label' => 'School',
          'description' => 'A school.',
        ),
      ),
    ),
    3 =>
    array (
      'name' => 'GovernmentOrganization',
      'label' => 'Government Organization',
      'description' => 'A governmental organization or agency.',
      'children' =>
      array (
      ),
    ),
    4 =>
    array (
      'name' => 'LocalBusiness',
      'label' => 'Local Business',
      'description' => 'A particular physical business or branch of an organization. Examples of LocalBusiness include a restaurant, a particular branch of a restaurant chain, a branch of a bank, a medical practice, a club, a bowling alley, etc.',
      'children' =>
      array (
        0 =>
        array (
          'name' => 'AnimalShelter',
          'label' => 'Animal Shelter',
          'description' => 'Animal shelter.',
        ),
        1 =>
        array (
          'name' => 'AutomotiveBusiness',
          'label' => 'Automotive Business',
          'description' => 'Car repair, sales, or parts.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'AutoBodyShop',
              'label' => 'Auto Body Shop',
              'description' => 'Auto body shop.',
            ),
            1 =>
            array (
              'name' => 'AutoDealer',
              'label' => 'Auto Dealer',
              'description' => 'An car dealership.',
            ),
            2 =>
            array (
              'name' => 'AutoPartsStore',
              'label' => 'Auto Parts Store',
              'description' => 'An auto parts store.',
            ),
            3 =>
            array (
              'name' => 'AutoRental',
              'label' => 'Auto Rental',
              'description' => 'A car rental business.',
            ),
            4 =>
            array (
              'name' => 'AutoRepair',
              'label' => 'Auto Repair',
              'description' => 'Car repair business.',
            ),
            5 =>
            array (
              'name' => 'AutoWash',
              'label' => 'Auto Wash',
              'description' => 'A car wash business.',
            ),
            6 =>
            array (
              'name' => 'GasStation',
              'label' => 'Gas Station',
              'description' => 'A gas station.',
            ),
            7 =>
            array (
              'name' => 'MotorcycleDealer',
              'label' => 'Motorcycle Dealer',
              'description' => 'A motorcycle dealer.',
            ),
            8 =>
            array (
              'name' => 'MotorcycleRepair',
              'label' => 'Motorcycle Repair',
              'description' => 'A motorcycle repair shop.',
            ),
          ),
        ),
        2 =>
        array (
          'name' => 'ChildCare',
          'label' => 'Child Care',
          'description' => 'A Childcare center.',
        ),
        3 =>
        array (
          'name' => 'Dentist',
          'label' => 'Dentist',
          'description' => 'A dentist.',
        ),
        4 =>
        array (
          'name' => 'DryCleaningOrLaundry',
          'label' => 'Dry Cleaning Or Laundry',
          'description' => 'A dry-cleaning business.',
        ),
        5 =>
        array (
          'name' => 'EmergencyService',
          'label' => 'Emergency Service',
          'description' => 'An emergency service, such as a fire station or ER.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'FireStation',
              'label' => 'Fire Station',
              'description' => 'A fire station. With firemen.',
            ),
            1 =>
            array (
              'name' => 'Hospital',
              'label' => 'Hospital',
              'description' => 'A hospital.',
            ),
            2 =>
            array (
              'name' => 'PoliceStation',
              'label' => 'Police Station',
              'description' => 'A police station.',
            ),
          ),
        ),
        6 =>
        array (
          'name' => 'EmploymentAgency',
          'label' => 'Employment Agency',
          'description' => 'An employment agency.',
        ),
        7 =>
        array (
          'name' => 'EntertainmentBusiness',
          'label' => 'Entertainment Business',
          'description' => 'A business providing entertainment.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'AdultEntertainment',
              'label' => 'Adult Entertainment',
              'description' => 'An adult entertainment establishment.',
            ),
            1 =>
            array (
              'name' => 'AmusementPark',
              'label' => 'Amusement Park',
              'description' => 'An amusement park.',
            ),
            2 =>
            array (
              'name' => 'ArtGallery',
              'label' => 'Art Gallery',
              'description' => 'An art gallery.',
            ),
            3 =>
            array (
              'name' => 'Casino',
              'label' => 'Casino',
              'description' => 'A casino.',
            ),
            4 =>
            array (
              'name' => 'ComedyClub',
              'label' => 'Comedy Club',
              'description' => 'A comedy club.',
            ),
            5 =>
            array (
              'name' => 'MovieTheater',
              'label' => 'Movie Theater',
              'description' => 'A movie theater.',
            ),
            6 =>
            array (
              'name' => 'NightClub',
              'label' => 'Night Club',
              'description' => 'A nightclub or discotheque.',
            ),
          ),
        ),
        8 =>
        array (
          'name' => 'FinancialService',
          'label' => 'Financial Service',
          'description' => 'Financial services business.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'AccountingService',
              'label' => 'Accounting Service',
              'description' => 'Accountancy business.

As a LocalBusiness it can be described as a provider of one or more Service(s).',
            ),
            1 =>
            array (
              'name' => 'AutomatedTeller',
              'label' => 'Automated Teller',
              'description' => 'ATM/cash machine.',
            ),
            2 =>
            array (
              'name' => 'BankOrCreditUnion',
              'label' => 'Bank Or Credit Union',
              'description' => 'Bank or credit union.',
            ),
            3 =>
            array (
              'name' => 'InsuranceAgency',
              'label' => 'Insurance Agency',
              'description' => 'An Insurance agency.',
            ),
          ),
        ),
        9 =>
        array (
          'name' => 'FoodEstablishment',
          'label' => 'Food Establishment',
          'description' => 'A food-related business.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'Bakery',
              'label' => 'Bakery',
              'description' => 'A bakery.',
            ),
            1 =>
            array (
              'name' => 'BarOrPub',
              'label' => 'Bar Or Pub',
              'description' => 'A bar or pub.',
            ),
            2 =>
            array (
              'name' => 'Brewery',
              'label' => 'Brewery',
              'description' => 'Brewery.',
            ),
            3 =>
            array (
              'name' => 'CafeOrCoffeeShop',
              'label' => 'Cafe Or Coffee Shop',
              'description' => 'A cafe or coffee shop.',
            ),
            4 =>
            array (
              'name' => 'Distillery',
              'label' => 'Distillery',
              'description' => 'A distillery.',
            ),
            5 =>
            array (
              'name' => 'FastFoodRestaurant',
              'label' => 'Fast Food Restaurant',
              'description' => 'A fast-food restaurant.',
            ),
            6 =>
            array (
              'name' => 'IceCreamShop',
              'label' => 'Ice Cream Shop',
              'description' => 'An ice cream shop.',
            ),
            7 =>
            array (
              'name' => 'Restaurant',
              'label' => 'Restaurant',
              'description' => 'A restaurant.',
            ),
            8 =>
            array (
              'name' => 'Winery',
              'label' => 'Winery',
              'description' => 'A winery.',
            ),
          ),
        ),
        10 =>
        array (
          'name' => 'GovernmentOffice',
          'label' => 'Government Office',
          'description' => 'A government office&#x2014;for example, an IRS or DMV office.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'PostOffice',
              'label' => 'Post Office',
              'description' => 'A post office.',
            ),
          ),
        ),
        11 =>
        array (
          'name' => 'HealthAndBeautyBusiness',
          'label' => 'Health And Beauty Business',
          'description' => 'Health and beauty.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'BeautySalon',
              'label' => 'Beauty Salon',
              'description' => 'Beauty salon.',
            ),
            1 =>
            array (
              'name' => 'DaySpa',
              'label' => 'Day Spa',
              'description' => 'A day spa.',
            ),
            2 =>
            array (
              'name' => 'HairSalon',
              'label' => 'Hair Salon',
              'description' => 'A hair salon.',
            ),
            3 =>
            array (
              'name' => 'HealthClub',
              'label' => 'Health Club',
              'description' => 'A health club.',
            ),
            4 =>
            array (
              'name' => 'NailSalon',
              'label' => 'Nail Salon',
              'description' => 'A nail salon.',
            ),
            5 =>
            array (
              'name' => 'TattooParlor',
              'label' => 'Tattoo Parlor',
              'description' => 'A tattoo parlor.',
            ),
          ),
        ),
        12 =>
        array (
          'name' => 'HomeAndConstructionBusiness',
          'label' => 'Home And Construction Business',
          'description' => 'A construction business.

A HomeAndConstructionBusiness is a LocalBusiness that provides services around homes and buildings...',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'Electrician',
              'label' => 'Electrician',
              'description' => 'An electrician.',
            ),
            1 =>
            array (
              'name' => 'GeneralContractor',
              'label' => 'General Contractor',
              'description' => 'A general contractor.',
            ),
            2 =>
            array (
              'name' => 'HVACBusiness',
              'label' => 'HVACBusiness',
              'description' => 'A business that provide Heating, Ventilation and Air Conditioning services.',
            ),
            3 =>
            array (
              'name' => 'HousePainter',
              'label' => 'House Painter',
              'description' => 'A house painting service.',
            ),
            4 =>
            array (
              'name' => 'Locksmith',
              'label' => 'Locksmith',
              'description' => 'A locksmith.',
            ),
            5 =>
            array (
              'name' => 'MovingCompany',
              'label' => 'Moving Company',
              'description' => 'A moving company.',
            ),
            6 =>
            array (
              'name' => 'Plumber',
              'label' => 'Plumber',
              'description' => 'A plumbing service.',
            ),
            7 =>
            array (
              'name' => 'RoofingContractor',
              'label' => 'Roofing Contractor',
              'description' => 'A roofing contractor.',
            ),
          ),
        ),
        13 =>
        array (
          'name' => 'InternetCafe',
          'label' => 'Internet Cafe',
          'description' => 'An internet cafe.',
        ),
        14 =>
        array (
          'name' => 'LegalService',
          'label' => 'Legal Service',
          'description' => 'A LegalService is a business that provides legally-oriented services, advice and representation, e...',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'Attorney',
              'label' => 'Attorney',
              'description' => 'Professional service: Attorney.

This type is deprecated - LegalService is more inclusive and less ambiguous.',
            ),
            1 =>
            array (
              'name' => 'Notary',
              'label' => 'Notary',
              'description' => 'A notary.',
            ),
          ),
        ),
        15 =>
        array (
          'name' => 'Library',
          'label' => 'Library',
          'description' => 'A library.',
        ),
        16 =>
        array (
          'name' => 'LodgingBusiness',
          'label' => 'Lodging Business',
          'description' => 'A lodging business, such as a motel, hotel, or inn.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'BedAndBreakfast',
              'label' => 'Bed And Breakfast',
              'description' => 'Bed and breakfast.

See also the dedicated document on the use of schema...',
            ),
            1 =>
            array (
              'name' => 'Campground',
              'label' => 'Campground',
              'description' => 'A camping site, campsite, or campground is a place used for overnight stay in the outdoors...',
            ),
            2 =>
            array (
              'name' => 'Hostel',
              'label' => 'Hostel',
              'description' => 'A hostel - cheap accommodation, often in shared dormitories.

See also the dedicated document on the use of schema...',
            ),
            3 =>
            array (
              'name' => 'Hotel',
              'label' => 'Hotel',
              'description' => 'A hotel is an establishment that provides lodging paid on a short-term basis (Source: Wikipedia, the free encyclopedia, see http://en...',
            ),
            4 =>
            array (
              'name' => 'Motel',
              'label' => 'Motel',
              'description' => 'A motel.

See also the dedicated document on the use of schema...',
            ),
            5 =>
            array (
              'name' => 'Resort',
              'label' => 'Resort',
              'description' => 'A resort is a place used for relaxation or recreation, attracting visitors for holidays or vacations...',
            ),
          ),
        ),
        17 =>
        array (
          'name' => 'MedicalBusiness',
          'label' => 'Medical Business',
          'description' => 'A particular physical or virtual business of an organization for medical purposes...',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'CommunityHealth',
              'label' => 'Community Health',
              'description' => 'A field of public health focusing on improving health characteristics of a defined population in relation with their geographical or environment areas',
            ),
            1 =>
            array (
              'name' => 'Dermatology',
              'label' => 'Dermatology',
              'description' => 'A specific branch of medical science that pertains to diagnosis and treatment of disorders of skin.',
            ),
            2 =>
            array (
              'name' => 'DietNutrition',
              'label' => 'Diet Nutrition',
              'description' => 'Dietetic and nutrition as a medical speciality.',
            ),
            3 =>
            array (
              'name' => 'Emergency',
              'label' => 'Emergency',
              'description' => 'A specific branch of medical science that deals with the evaluation and initial treatment of medical conditions caused by trauma or sudden illness.',
            ),
            4 =>
            array (
              'name' => 'Geriatric',
              'label' => 'Geriatric',
              'description' => 'A specific branch of medical science that is concerned with the diagnosis and treatment of diseases, debilities and provision of care to the aged.',
            ),
            5 =>
            array (
              'name' => 'Gynecologic',
              'label' => 'Gynecologic',
              'description' => 'A specific branch of medical science that pertains to the health care of women, particularly in the diagnosis and treatment of disorders affecting the female reproductive system.',
            ),
            6 =>
            array (
              'name' => 'MedicalClinic',
              'label' => 'Medical Clinic',
              'description' => 'A facility, often associated with a hospital or medical school, that is devoted to the specific diagnosis and/or healthcare...',
            ),
            7 =>
            array (
              'name' => 'Midwifery',
              'label' => 'Midwifery',
              'description' => 'A nurse-like health profession that deals with pregnancy, childbirth, and the postpartum period (including care of the newborn), besides sexual and reproductive health of women throughout their lives.',
            ),
            8 =>
            array (
              'name' => 'Nursing',
              'label' => 'Nursing',
              'description' => 'A health profession of a person formally educated and trained in the care of the sick or infirm person.',
            ),
            9 =>
            array (
              'name' => 'Obstetric',
              'label' => 'Obstetric',
              'description' => 'A specific branch of medical science that specializes in the care of women during the prenatal and postnatal care and with the delivery of the child.',
            ),
            10 =>
            array (
              'name' => 'Oncologic',
              'label' => 'Oncologic',
              'description' => 'A specific branch of medical science that deals with benign and malignant tumors, including the study of their development, diagnosis, treatment and prevention.',
            ),
            11 =>
            array (
              'name' => 'Optician',
              'label' => 'Optician',
              'description' => 'A store that sells reading glasses and similar devices for improving vision.',
            ),
            12 =>
            array (
              'name' => 'Optometric',
              'label' => 'Optometric',
              'description' => 'The science or practice of testing visual acuity and prescribing corrective lenses.',
            ),
            13 =>
            array (
              'name' => 'Otolaryngologic',
              'label' => 'Otolaryngologic',
              'description' => 'A specific branch of medical science that is concerned with the ear, nose and throat and their respective disease states.',
            ),
            14 =>
            array (
              'name' => 'Pediatric',
              'label' => 'Pediatric',
              'description' => 'A specific branch of medical science that specializes in the care of infants, children and adolescents.',
            ),
            15 =>
            array (
              'name' => 'Pharmacy',
              'label' => 'Pharmacy',
              'description' => 'A pharmacy or drugstore.',
            ),
            16 =>
            array (
              'name' => 'Physician',
              'label' => 'Physician',
              'description' => 'A doctor\'s office.',
            ),
            17 =>
            array (
              'name' => 'Physiotherapy',
              'label' => 'Physiotherapy',
              'description' => 'The practice of treatment of disease, injury, or deformity by physical methods such as massage, heat treatment, and exercise rather than by drugs or surgery.',
            ),
            18 =>
            array (
              'name' => 'PlasticSurgery',
              'label' => 'Plastic Surgery',
              'description' => 'A specific branch of medical science that pertains to therapeutic or cosmetic repair or re-formation of missing, injured or malformed tissues or body parts by manual and instrumental means.',
            ),
            19 =>
            array (
              'name' => 'Podiatric',
              'label' => 'Podiatric',
              'description' => 'Podiatry is the care of the human foot, especially the diagnosis and treatment of foot disorders.',
            ),
            20 =>
            array (
              'name' => 'PrimaryCare',
              'label' => 'Primary Care',
              'description' => 'The medical care by a physician, or other health-care professional, who is the patient\'s first contact with the health-care system and who may recommend a specialist if necessary.',
            ),
            21 =>
            array (
              'name' => 'Psychiatric',
              'label' => 'Psychiatric',
              'description' => 'A specific branch of medical science that is concerned with the study, treatment, and prevention of mental illness, using both medical and psychological therapies.',
            ),
            22 =>
            array (
              'name' => 'PublicHealth',
              'label' => 'Public Health',
              'description' => 'Branch of medicine that pertains to the health services to improve and protect community health, especially epidemiology, sanitation, immunization, and preventive medicine.',
            ),
          ),
        ),
        18 =>
        array (
          'name' => 'ProfessionalService',
          'label' => 'Professional Service',
          'description' => 'Original definition: "provider of professional services."

The general ProfessionalService type for local businesses was deprecated due to confusion with Service...',
        ),
        19 =>
        array (
          'name' => 'RadioStation',
          'label' => 'Radio Station',
          'description' => 'A radio station.',
        ),
        20 =>
        array (
          'name' => 'RealEstateAgent',
          'label' => 'Real Estate Agent',
          'description' => 'A real-estate agent.',
        ),
        21 =>
        array (
          'name' => 'RecyclingCenter',
          'label' => 'Recycling Center',
          'description' => 'A recycling center.',
        ),
        22 =>
        array (
          'name' => 'SelfStorage',
          'label' => 'Self Storage',
          'description' => 'A self-storage facility.',
        ),
        23 =>
        array (
          'name' => 'ShoppingCenter',
          'label' => 'Shopping Center',
          'description' => 'A shopping center or mall.',
        ),
        24 =>
        array (
          'name' => 'SportsActivityLocation',
          'label' => 'Sports Activity Location',
          'description' => 'A sports location, such as a playing field.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'BowlingAlley',
              'label' => 'Bowling Alley',
              'description' => 'A bowling alley.',
            ),
            1 =>
            array (
              'name' => 'ExerciseGym',
              'label' => 'Exercise Gym',
              'description' => 'A gym.',
            ),
            2 =>
            array (
              'name' => 'GolfCourse',
              'label' => 'Golf Course',
              'description' => 'A golf course.',
            ),
            3 =>
            array (
              'name' => 'PublicSwimmingPool',
              'label' => 'Public Swimming Pool',
              'description' => 'A public swimming pool.',
            ),
            4 =>
            array (
              'name' => 'SkiResort',
              'label' => 'Ski Resort',
              'description' => 'A ski resort.',
            ),
            5 =>
            array (
              'name' => 'SportsClub',
              'label' => 'Sports Club',
              'description' => 'A sports club.',
            ),
            6 =>
            array (
              'name' => 'StadiumOrArena',
              'label' => 'Stadium Or Arena',
              'description' => 'A stadium.',
            ),
            7 =>
            array (
              'name' => 'TennisComplex',
              'label' => 'Tennis Complex',
              'description' => 'A tennis complex.',
            ),
          ),
        ),
        25 =>
        array (
          'name' => 'Store',
          'label' => 'Store',
          'description' => 'A retail good store.',
          'children' =>
          array (
            0 =>
            array (
              'name' => 'BikeStore',
              'label' => 'Bike Store',
              'description' => 'A bike store.',
            ),
            1 =>
            array (
              'name' => 'BookStore',
              'label' => 'Book Store',
              'description' => 'A bookstore.',
            ),
            2 =>
            array (
              'name' => 'ClothingStore',
              'label' => 'Clothing Store',
              'description' => 'A clothing store.',
            ),
            3 =>
            array (
              'name' => 'ComputerStore',
              'label' => 'Computer Store',
              'description' => 'A computer store.',
            ),
            4 =>
            array (
              'name' => 'ConvenienceStore',
              'label' => 'Convenience Store',
              'description' => 'A convenience store.',
            ),
            5 =>
            array (
              'name' => 'DepartmentStore',
              'label' => 'Department Store',
              'description' => 'A department store.',
            ),
            6 =>
            array (
              'name' => 'ElectronicsStore',
              'label' => 'Electronics Store',
              'description' => 'An electronics store.',
            ),
            7 =>
            array (
              'name' => 'Florist',
              'label' => 'Florist',
              'description' => 'A florist.',
            ),
            8 =>
            array (
              'name' => 'FurnitureStore',
              'label' => 'Furniture Store',
              'description' => 'A furniture store.',
            ),
            9 =>
            array (
              'name' => 'GardenStore',
              'label' => 'Garden Store',
              'description' => 'A garden store.',
            ),
            10 =>
            array (
              'name' => 'GroceryStore',
              'label' => 'Grocery Store',
              'description' => 'A grocery store.',
            ),
            11 =>
            array (
              'name' => 'HardwareStore',
              'label' => 'Hardware Store',
              'description' => 'A hardware store.',
            ),
            12 =>
            array (
              'name' => 'HobbyShop',
              'label' => 'Hobby Shop',
              'description' => 'A store that sells materials useful or necessary for various hobbies.',
            ),
            13 =>
            array (
              'name' => 'HomeGoodsStore',
              'label' => 'Home Goods Store',
              'description' => 'A home goods store.',
            ),
            14 =>
            array (
              'name' => 'JewelryStore',
              'label' => 'Jewelry Store',
              'description' => 'A jewelry store.',
            ),
            15 =>
            array (
              'name' => 'LiquorStore',
              'label' => 'Liquor Store',
              'description' => 'A shop that sells alcoholic drinks such as wine, beer, whisky and other spirits.',
            ),
            16 =>
            array (
              'name' => 'MensClothingStore',
              'label' => 'Mens Clothing Store',
              'description' => 'A men\'s clothing store.',
            ),
            17 =>
            array (
              'name' => 'MobilePhoneStore',
              'label' => 'Mobile Phone Store',
              'description' => 'A store that sells mobile phones and related accessories.',
            ),
            18 =>
            array (
              'name' => 'MovieRentalStore',
              'label' => 'Movie Rental Store',
              'description' => 'A movie rental store.',
            ),
            19 =>
            array (
              'name' => 'MusicStore',
              'label' => 'Music Store',
              'description' => 'A music store.',
            ),
            20 =>
            array (
              'name' => 'OfficeEquipmentStore',
              'label' => 'Office Equipment Store',
              'description' => 'An office equipment store.',
            ),
            21 =>
            array (
              'name' => 'OutletStore',
              'label' => 'Outlet Store',
              'description' => 'An outlet store.',
            ),
            22 =>
            array (
              'name' => 'PawnShop',
              'label' => 'Pawn Shop',
              'description' => 'A shop that will buy, or lend money against the security of, personal possessions.',
            ),
            23 =>
            array (
              'name' => 'PetStore',
              'label' => 'Pet Store',
              'description' => 'A pet store.',
            ),
            24 =>
            array (
              'name' => 'ShoeStore',
              'label' => 'Shoe Store',
              'description' => 'A shoe store.',
            ),
            25 =>
            array (
              'name' => 'SportingGoodsStore',
              'label' => 'Sporting Goods Store',
              'description' => 'A sporting goods store.',
            ),
            26 =>
            array (
              'name' => 'TireShop',
              'label' => 'Tire Shop',
              'description' => 'A tire shop.',
            ),
            27 =>
            array (
              'name' => 'ToyStore',
              'label' => 'Toy Store',
              'description' => 'A toy store.',
            ),
            28 =>
            array (
              'name' => 'WholesaleStore',
              'label' => 'Wholesale Store',
              'description' => 'A wholesale store.',
            ),
          ),
        ),
        26 =>
        array (
          'name' => 'TelevisionStation',
          'label' => 'Television Station',
          'description' => 'A television station.',
        ),
        27 =>
        array (
          'name' => 'TouristInformationCenter',
          'label' => 'Tourist Information Center',
          'description' => 'A tourist information center.',
        ),
        28 =>
        array (
          'name' => 'TravelAgency',
          'label' => 'Travel Agency',
          'description' => 'A travel agency.',
        ),
      ),
    ),
    5 =>
    array (
      'name' => 'MedicalOrganization',
      'label' => 'Medical Organization',
      'description' => 'A medical organization (physical or not), such as hospital, institution or clinic.',
      'children' =>
      array (
        0 =>
        array (
          'name' => 'DiagnosticLab',
          'label' => 'Diagnostic Lab',
          'description' => 'A medical laboratory that offers on-site or off-site diagnostic services.',
        ),
        1 =>
        array (
          'name' => 'VeterinaryCare',
          'label' => 'Veterinary Care',
          'description' => 'A vet\'s office.',
        ),
      ),
    ),
    6 =>
    array (
      'name' => 'NGO',
      'label' => 'NGO',
      'description' => 'Organization: Non-governmental Organization.',
      'children' =>
      array (
      ),
    ),
    7 =>
    array (
      'name' => 'PerformingGroup',
      'label' => 'Performing Group',
      'description' => 'A performance group, such as a band, an orchestra, or a circus.',
      'children' =>
      array (
        0 =>
        array (
          'name' => 'DanceGroup',
          'label' => 'Dance Group',
          'description' => 'A dance group&#x2014;for example, the Alvin Ailey Dance Theater or Riverdance.',
        ),
        1 =>
        array (
          'name' => 'MusicGroup',
          'label' => 'Music Group',
          'description' => 'A musical group, such as a band, an orchestra, or a choir. Can also be a solo musician.',
        ),
        2 =>
        array (
          'name' => 'TheaterGroup',
          'label' => 'Theater Group',
          'description' => 'A theater group or company, for example, the Royal Shakespeare Company or Druid Theatre.',
        ),
      ),
    ),
    8 =>
    array (
      'name' => 'SportsOrganization',
      'label' => 'Sports Organization',
      'description' => 'Represents the collection of all sports organizations, including sports teams, governing bodies, and sports associations.',
      'children' =>
      array (
        0 =>
        array (
          'name' => 'SportsTeam',
          'label' => 'Sports Team',
          'description' => 'Organization: Sports team.',
        ),
      ),
    ),
    9 =>
    array (
      'name' => 'WorkersUnion',
      'label' => 'Workers Union',
      'description' => 'A Workers Union (also known as a Labor Union, Labour Union, or Trade Union) is an organization that promotes the interests of its worker members by collectively bargaining with management, organizing, and political lobbying.',
      'children' =>
      array (
      ),
    ),
  ),
);
