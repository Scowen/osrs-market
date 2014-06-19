# Ascot Racing
This project's purpose is to help with a friends party where bets are taken on the horses at the Ascot races on Saturday 21st June 2014.   

This project is mainly written in PHP using the Yii Framework. It also utilizes Twitters Bootstrap for the frontend.

## Database Merge & Migration

Follow these steps to successfully migrate and merge, from scratch (using Git Bash):  

1. cd <path to directory>
2. composer update
3. mysql -u root
4. create database ascot;
5. exit;
6. cd <path to project>
7. ./application/yiic migrate

Complete!

## Setting up the database environment  

Follow these steps to create the correct database environments:  

1. Create the file "databases.php" in "/application/config"
2. The file contents:  
```
    return array(
        'develop' => array(
            'connectionString' => 'mysql:host=localhost;dbname=ascot',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => '',
        ),
    );
```
3. Create the file ".environment" in "/application/config"
4. The file contents:  
``
    develop
``

Complete!


## Authors

- [Luke Scowen][scowen]; Project Lead.

[scowen]: http://www.github.com/scowen "Luke Scowen"
