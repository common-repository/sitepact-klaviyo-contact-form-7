<?php
/**
* Were we have the structure of the profile files to show on the contact form 7 config panel
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

    function klaviyo_regular_fields_model(){
        $klaviyo_regular_fields = array(
            [
                'name' => 'Email',
                'key' => 'profile_email',
                'required' => true
            ],
            [
                'name' => 'First Name',
                'key'  => 'profile_first_name',
                'required' => false
            ],
            [
                'name' => 'Last Name',
                'key'  => 'profile_last_name',
                'required' => false
            ],
            [
                'name' => 'Organization',
                'key'  => 'profile_organization',
                'required' => false
            ],
            [
                'name' => 'Title',
                'key'  => 'profile_title',
                'required' => false
            ],
            [
                'name' => 'Phone',
                'key'  => 'profile_phone_number',
                'required' => false
            ],
            [
                'name' => 'Address 1',
                'key'  => 'profile_location_address1',
                'required' => false
            ],
            [
                'name' => 'Address 2',
                'key'  => 'profile_location_address2',
                'required' => false
            ],
            [
                'name' => 'City',
                'key'  => 'profile_location_city',
                'required' => false
            ],
            [
                'name' => 'Country',
                'key'  => 'profile_location_country',
                'required' => false
            ],
                        [
                'name' => 'Region',
                'key'  => 'profile_location_region',
                'required' => false
            ],
            [
                'name' => 'Zip',
                'key'  => 'profile_location_zip',
                'required' => false
            ],
            [
                'name' => 'Latitude',
                'key'  => 'profile_location_latitude',
                'required' => false
            ],
            [
                'name' => 'Longitude',
                'key'  => 'profile_location_longitude',
                'required' => false
            ],
            [
                'name' => 'Timezone',
                'key'  => 'profile_location_timezone',
                'required' => false
            ],
            [
                'name' => 'IP Address',
                'key'  => 'profile_location_ip',
                'required' => false
            ],
            [
                'name' => 'External ID',
                'key'  => 'profile_externalid',
                'required' => false
            ],
        );
        return $klaviyo_regular_fields;
    }

    function klaviyo_create_profile_model(){
        $klaviyo_create_profile_fields = [
            "type" => "profile",
            "attributes" => [
                "email" => null,
                "phone_number" => null,
                "external_id" => null,
                "first_name" => null,
                "last_name" => null,
                "organization" => null,
                "title" => null,
                "image" => null,
                "location" => [
                    "address1" => null,
                    "address2" => null,
                    "city" => null,
                    "country" => null,
                    "latitude" => null,
                    "longitude" => null,
                    "region" => null,
                    "zip" => null,
                    "timezone" => null,
                    "ip" => null
                ],
                "properties" => []
            ]
        ];
        return $klaviyo_create_profile_fields;
    }

    function klaviyo_subscribe_email_model(){
        $array = [
            "data" => [
                "type" => "profile-subscription-bulk-create-job",
                "attributes" => [
                    "custom_source" => null,
                    "profiles" => [
                        "data" => [
                            [
                                "type" => "profile",
                                "id" => null,
                                "attributes" => [
                                    "email" => null,
                                    "subscriptions" => [
                                        "email" => [
                                            "marketing" => [
                                                "consent" => "SUBSCRIBED"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "relationships" => [
                    "list" => [
                        "data" => [
                            "type" => "list",
                            "id" => null
                        ]
                    ]
                ]
            ]
        ];
        return $array;
    }