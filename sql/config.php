<?php
// ===========================================================================================
//
// config.php
//
// Config-file for database and SQL related issues. All SQL-statements are usually stored in this
// directory (TP_SQLPATH). This files contains global definitions for table names and so.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Define the names for the database (tables, views, procedures, functions, triggers)
//
define('DBT_User',                 DB_PREFIX . 'User');
define('DBT_Group',             DB_PREFIX . 'Group');
define('DBT_GroupMember',    DB_PREFIX . 'GroupMember');
define('DBT_Statistics',    DB_PREFIX . 'Statistics');
define('DBT_Article',            DB_PREFIX . 'Article');
define('DBT_Topic',                DB_PREFIX . 'Topic');
define('DBT_Topic2Post',    DB_PREFIX . 'Topic2Post');

// Stored procedures
define('DBSP_PGetArticleDetailsAndArticleList',    DB_PREFIX . 'PGetArticleDetailsAndArticleList');
define('DBSP_PGetArticleList',                                    DB_PREFIX . 'PGetArticleList');
define('DBSP_PGetArticleDetails',                                DB_PREFIX . 'PGetArticleDetails');
define('DBSP_PInsertOrUpdateArticle',                        DB_PREFIX . 'PInsertOrUpdateArticle');
define('DBSP_PGetTopicList',                                        DB_PREFIX . 'PGetTopicList');
define('DBSP_PGetTopicDetails',                                    DB_PREFIX . 'PGetTopicDetails');
define('DBSP_PGetTopicDetailsAndPosts',                    DB_PREFIX . 'PGetTopicDetailsAndPosts');
define('DBSP_PGetPostDetails',                                    DB_PREFIX . 'PGetPostDetails');
define('DBSP_PInsertOrUpdatePost',                            DB_PREFIX . 'PInsertOrUpdatepost');
define('DBSP_PInitialPostPublish',                            DB_PREFIX . 'PInitialPostPublish');

// User Defined Functions UDF
define('DBUDF_FCheckUserIsOwnerOrAdmin',    DB_PREFIX . 'FCheckUserIsOwnerOrAdmin');

// Triggers
define('DBTR_TInsertUser',        DB_PREFIX . 'TInsertUser');
define('DBTR_TAddArticle',        DB_PREFIX . 'TAddArticle');


?>