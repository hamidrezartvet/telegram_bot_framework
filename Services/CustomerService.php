<?php

    class CustomerService{        

        /**
         * Class introduction: this class is written for customer management in mysql database.
         * If tour bot need create,update,get and delete customers in local mysql database , 
         * This Service class can help you.
         * I use /root/Service/DatbaseService.php for database connection.
         * DatabseService.php object is introduced and use in this class with dependency injection based on OOP standards.
         */


        /**
         * Define variables here
         */
        private $databaseService;


        /**
         * Define construct
         */
        function __construct(DatabaseService $databaseService)
        {

            $this->databaseService = $databaseService;
        }
    }