<?php
// ===========================================================================================
//
// SQLCreateArticleTable.php
//
// SQL statements to create the tables for the Article tables.
//
// WARNING: Do not forget to check input variables for SQL injections.
//
// Author: Mikael Roos
//


// Get the tablenames
$tArticle = DBT_Article;
$tTopic = DBT_Topic;
$tTopic2Post = DBT_Topic2Post;
$tUser = DBT_User;
$tGroup = DBT_Group;
$tGroupMember = DBT_GroupMember;
$tStatistics = DBT_Statistics;

// Get the SP names
$spPGetArticleDetailsAndArticleList = DBSP_PGetArticleDetailsAndArticleList;
$spPGetArticleList = DBSP_PGetArticleList;
$spPGetArticleDetails = DBSP_PGetArticleDetails;
$spPInsertOrUpdateArticle = DBSP_PInsertOrUpdateArticle;
$spPGetTopicList = DBSP_PGetTopicList;
$spPGetTopicDetails = DBSP_PGetTopicDetails;
$spPGetTopicDetailsAndPosts = DBSP_PGetTopicDetailsAndPosts;
$spPGetPostDetails = DBSP_PGetPostDetails;
$spPInsertOrUpdatePost = DBSP_PInsertOrUpdatePost;
$spPInitialPostPublish = DBSP_PInitialPostPublish;

// Get the UDF names
$udfFCheckUserIsOwnerOrAdmin = DBUDF_FCheckUserIsOwnerOrAdmin;

// Get the trigger names
$trAddArticle = DBTR_TAddArticle;

// Create the query
$query = <<<EOD

-- =============================================================================================
--
-- SQL for Article
--
-- =============================================================================================


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for the Article
--
DROP TABLE IF EXISTS {$tArticle};
CREATE TABLE {$tArticle} (

-- Primary key(s)
idArticle INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

-- Foreign keys
Article_idUser INT NOT NULL,
FOREIGN KEY (Article_idUser) REFERENCES {$tUser}(idUser),

titleArticle VARCHAR(256) NULL,
contentArticle BLOB NULL,
createdArticle DATETIME NOT NULL,
modifiedArticle DATETIME NULL,
deletedArticle DATETIME NULL,

-- Attributes to enable draft, publish and autosaves
draftTitleArticle VARCHAR(256) NULL,
draftContentArticle BLOB NULL,
draftModifiedArticle DATETIME NULL,
publishedArticle DATETIME NULL

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert or update article
-- If article id is 0 then insert, else update
--
DROP PROCEDURE IF EXISTS {$spPInsertOrUpdateArticle};
CREATE PROCEDURE {$spPInsertOrUpdateArticle}
(
INOUT aArticleId INT,
IN aUserId INT,
IN aTitle VARCHAR(256),
IN aContent BLOB
)
BEGIN
IF aArticleId = 0 THEN
BEGIN
INSERT INTO {$tArticle}
(Article_idUser, titleArticle, contentArticle, createdArticle)
VALUES
(aUserId, aTitle, aContent, NOW());
SET aArticleId = LAST_INSERT_ID();
END;
ELSE
BEGIN
UPDATE {$tArticle} SET
titleArticle = aTitle,
contentArticle = aContent,
modifiedArticle = NOW()
WHERE
idArticle = aArticleId AND
{$udfFCheckUserIsOwnerOrAdmin}(aArticleId, aUserId)
LIMIT 1;
END;
END IF;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the contents of an article
--
DROP PROCEDURE IF EXISTS {$spPGetArticleDetails};
CREATE PROCEDURE {$spPGetArticleDetails}
(
IN aArticleId INT,
IN aUserId INT
)
BEGIN
SELECT
A.titleArticle AS title,
A.contentArticle AS content,
A.createdArticle AS created,
A.modifiedArticle AS modified,
COALESCE(A.modifiedArticle, A.createdArticle) AS latest,
U.nameUser AS username
FROM {$tArticle} AS A
INNER JOIN {$tUser} AS U
ON A.Article_idUser = U.idUser
WHERE
idArticle = aArticleId AND
deletedArticle IS NULL AND
{$udfFCheckUserIsOwnerOrAdmin}(aArticleId, aUserId);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to provide a list of the latest articles
--
-- Limit does not accept a varible
-- http://bugs.mysql.com/bug.php?id=11918
--
DROP PROCEDURE IF EXISTS {$spPGetArticleList};
CREATE PROCEDURE {$spPGetArticleList}
(
IN aUserId INT
)
BEGIN
SELECT
idArticle AS id,
titleArticle AS title,
COALESCE(modifiedArticle, createdArticle) AS latest
FROM {$tArticle}
WHERE
Article_idUser = aUserId AND
deletedArticle IS NULL
ORDER BY modifiedArticle, createdArticle
LIMIT 20;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the contents of an article and provide a list of the latest articles
--
DROP PROCEDURE IF EXISTS {$spPGetArticleDetailsAndArticleList};
CREATE PROCEDURE {$spPGetArticleDetailsAndArticleList}
(
IN aArticleId INT,
IN aUserId INT
)
BEGIN
CALL {$spPGetArticleDetails}(aArticleId, aUserId);
CALL {$spPGetArticleList}(aUserId);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Create UDF that checks if user owns article or is member of group adm.
--
DROP FUNCTION IF EXISTS {$udfFCheckUserIsOwnerOrAdmin};
CREATE FUNCTION {$udfFCheckUserIsOwnerOrAdmin}
(
aArticleId INT,
aUserId INT
)
RETURNS BOOLEAN
BEGIN
DECLARE isAdmin INT;
DECLARE isOwner INT;
SELECT idUser INTO isAdmin
FROM {$tUser} AS U
INNER JOIN {$tGroupMember} AS GM
ON U.idUser = GM.GroupMember_idUser
INNER JOIN {$tGroup} AS G
ON G.idGroup = GM.GroupMember_idGroup
WHERE
idGroup = 'adm' AND
idUser = aUserId;

SELECT idUser INTO isOwner
FROM {$tUser} AS U
INNER JOIN {$tArticle} AS A
ON U.idUser = A.Article_idUser
WHERE
idArticle = aArticleId AND
idUser = aUserId;
RETURN (isAdmin OR isOwner);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Create trigger for Statistics
-- Add +1 when new article is created
--
DROP TRIGGER IF EXISTS {$trAddArticle};
CREATE TRIGGER {$trAddArticle}
AFTER INSERT ON {$tArticle}
FOR EACH ROW
BEGIN
UPDATE {$tStatistics}
SET
numOfArticlesStatistics = numOfArticlesStatistics + 1
WHERE
Statistics_idUser = NEW.Article_idUser;
END;


-- =============================================================================================
--
-- SQL for Forum
--
-- =============================================================================================


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for Topic
--
-- A forum topic. To connect a topic to all its post, look in table Topic2Post. However, the
-- first post is stored in the topic, for convinience and reduce of joins when looking for the
-- title of the topic (which is stored in the initial post).
--
DROP TABLE IF EXISTS {$tTopic};
CREATE TABLE {$tTopic} (

--
-- Primary key(s)
--
idTopic INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

--
-- Foreign keys
--
-- The first topic post
Topic_idArticle INT NOT NULL,
FOREIGN KEY (Topic_idArticle) REFERENCES {$tArticle}(idArticle),
-- Last person who posted in this topic
lastPostByTopic INT NOT NULL,
FOREIGN KEY (lastPostByTopic) REFERENCES {$tUser}(idUser),
--
-- Attributes
--
-- Counts the numer of posts in this topic
counterTopic INT NOT NULL,
-- Last time for posting to this topic
lastPostWhenTopic DATETIME NOT NULL

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for Topic2Post
--
-- Connection between topics and posts.
--
DROP TABLE IF EXISTS {$tTopic2Post};
CREATE TABLE {$tTopic2Post} (

--
-- Primary key(s)
--
-- Se below, combined from the two foreign keys

--
-- Foreign keys
--
-- The Topic
Topic2Post_idTopic INT NOT NULL,
FOREIGN KEY (Topic2Post_idTopic) REFERENCES {$tTopic}(idTopic),
-- The Post
Topic2Post_idArticle INT NOT NULL,
FOREIGN KEY (Topic2Post_idArticle) REFERENCES {$tArticle}(idArticle),

-- Primary key(s)
PRIMARY KEY (Topic2Post_idTopic, Topic2Post_idArticle)

--
-- Attributes
--
-- No additional attributes
);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get a list of all the topics together with details on each topic.
--
DROP PROCEDURE IF EXISTS {$spPGetTopicList};
CREATE PROCEDURE {$spPGetTopicList} ()
BEGIN
SELECT
T.idTopic AS topicid,
T.counterTopic AS postcounter,
T.lastPostWhenTopic AS latest,
A.idArticle AS postid,
A.titleArticle AS title,
A.createdArticle AS created,
U.idUser AS userid,
U.accountUser AS username,
U1.accountUser AS latestby
FROM {$tTopic} AS T
INNER JOIN {$tArticle} AS A
ON T.Topic_idArticle = A.idArticle
INNER JOIN {$tUser} AS U
ON A.Article_idUser = U.idUser
INNER JOIN {$tUser} AS U1
ON T.lastPostByTopic = U1.idUser
ORDER BY lastPostWhenTopic DESC
;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get details of a topic.
--
-- If aTopicId is set, use that.
-- If not, try to find a topic using the aPostId, as a second way to find the topic details.
--
DROP PROCEDURE IF EXISTS {$spPGetTopicDetails};
CREATE PROCEDURE {$spPGetTopicDetails}
(
IN aTopicId INT,
IN aPostId INT
)
BEGIN
IF aTopicId = 0 THEN
BEGIN
SELECT Topic2Post_idTopic INTO aTopicId FROM {$tTopic2Post} WHERE Topic2Post_idArticle = aPostId;
END;
END IF;
--
-- Get the topic details
--
SELECT
T.idTopic AS topicid,
T.counterTopic AS postcounter,
T.lastPostWhenTopic AS lastpostwhen,
T.Topic_idArticle AS toppost,
A.titleArticle AS title,
A.createdArticle AS created,
A.modifiedArticle AS modified,
U.accountUser AS creator,
U.idUser AS creatorid,
U1.accountUser AS lastpostby
FROM {$tTopic} AS T
INNER JOIN {$tArticle} AS A
ON T.Topic_idArticle = A.idArticle
INNER JOIN {$tUser} AS U
ON A.Article_idUser = U.idUser
INNER JOIN {$tUser} AS U1
ON T.lastPostByTopic = U1.idUser
WHERE
T.idTopic = aTopicId
;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the content of a topic, both topic details and all the posts related to the topic.
--
DROP PROCEDURE IF EXISTS {$spPGetTopicDetailsAndPosts};
CREATE PROCEDURE {$spPGetTopicDetailsAndPosts}
(
IN aTopicId INT
)
BEGIN
--
-- Get the topic details
--
CALL {$spPGetTopicDetails}(aTopicId, 0);
--
-- Get the list of all posts related to this topic
--
SELECT
A.idArticle AS postid,
A.titleArticle AS title,
A.contentArticle AS content,
A.createdArticle AS created,
U.idUser AS userid,
U.accountUser AS username,
U.avatarUser AS avatar
FROM {$tTopic2Post} AS T2P
INNER JOIN {$tArticle} AS A
ON A.idArticle = T2P.Topic2Post_idArticle
INNER JOIN {$tUser} AS U
ON A.Article_idUser = U.idUser
WHERE
T2P.Topic2Post_idTopic = aTopicId AND
A.deletedArticle IS NULL AND
A.publishedArticle IS NOT NULL
ORDER BY createdArticle ASC
;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the details of a topic and a specific post.
--
DROP PROCEDURE IF EXISTS {$spPGetPostDetails};
CREATE PROCEDURE {$spPGetPostDetails}
(
IN aPostId INT
)
BEGIN
--
-- Get the post details
--
SELECT
A.idArticle AS postid,
A.titleArticle AS title,
A.contentArticle AS content,
A.createdArticle AS created,
A.modifiedArticle AS modified,
IF(publishedArticle IS NULL, 0, 1) AS isPublished,
IF(draftModifiedArticle IS NULL, 0, 1) AS hasDraft,
A.draftTitleArticle AS draftTitle,
A.draftContentArticle AS draftContent,
A.draftModifiedArticle AS draftModified
FROM {$tTopic2Post} AS T2P
INNER JOIN {$tArticle} AS A
ON A.idArticle = T2P.Topic2Post_idArticle
INNER JOIN {$tUser} AS U
ON A.Article_idUser = U.idUser
WHERE
A.idArticle = aPostId AND
A.deletedArticle IS NULL AND
A.publishedArticle IS NOT NULL
ORDER BY createdArticle ASC
;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP for the first time when the post is published. Create/update topic tables accordingly.
--
-- If aTopicId is 0 then insert new entry into topic-table.
-- Keep tables Topic and Topic2Post updated.
--
DROP PROCEDURE IF EXISTS {$spPInitialPostPublish};
CREATE PROCEDURE {$spPInitialPostPublish}
(
INOUT aTopicId INT,
IN aPostId INT,
IN aUserId INT
)
BEGIN
--
-- Is it a new topic? Then create the topic else update it.
--
IF aTopicId = 0 THEN
BEGIN
INSERT INTO {$tTopic}
(Topic_idArticle, counterTopic, lastPostWhenTopic, lastPostByTopic)
VALUES
(aPostId, 1, NOW(), aUserId);
SET aTopicId = LAST_INSERT_ID();
END;
--
-- Topic exists, just update it
--
ELSE
BEGIN
UPDATE {$tTopic} SET
counterTopic = counterTopic + 1,
lastPostWhenTopic = NOW(),
lastPostByTopic = aUserId
WHERE
idTopic = aTopicId
LIMIT 1;
END;
END IF;

--
-- First time this post is published, insert post entry in Topic2Post
--
INSERT INTO {$tTopic2Post}
(Topic2Post_idTopic, Topic2Post_idArticle)
VALUES
(aTopicId, aPostId);
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert or update a forum post.
--
-- If aPostId is 0 then insert a new post.
-- else update the post.
-- Save 'draft' or 'publish' the post depending on aAction.
-- A post must be published once before it can be viewed.
--
DROP PROCEDURE IF EXISTS {$spPInsertOrUpdatePost};
CREATE PROCEDURE {$spPInsertOrUpdatePost}
(
INOUT aPostId INT,
INOUT aTopicId INT,
OUT isPublished INT,
OUT hasDraft INT,
IN aUserId INT,
IN aTitle VARCHAR(256),
IN aContent BLOB,
IN aAction CHAR(7) -- 'draft' or 'publish'
)
BEGIN
DECLARE isPostPublished BOOLEAN;
--
-- First see if this is a completely new post, if it is, start by creating an empty post
--
IF aPostId = 0 THEN
BEGIN
INSERT INTO {$tArticle} (Article_idUser, createdArticle) VALUES (aUserId, NOW());
SET aPostId = LAST_INSERT_ID();
END;
END IF;

--
-- Are we just saving a draft?
--
IF aAction = 'draft' THEN
BEGIN
UPDATE {$tArticle} SET
draftTitleArticle = aTitle,
draftContentArticle = aContent,
draftModifiedArticle = NOW()
WHERE
idArticle = aPostId AND
{$udfFCheckUserIsOwnerOrAdmin}(aPostId, aUserId)
LIMIT 1;
END;

--
-- Or are we publishing the post? Then prepare it and remove the draft.
--
ELSEIF aAction = 'publish' THEN
BEGIN
--
-- Before we proceed, lets see if this post is published or not.
--
SELECT publishedArticle INTO isPostPublished FROM {$tArticle} WHERE idArticle = aPostId;

--
-- Need to do some extra work if this is the first time the post is published
--
IF isPostPublished IS NULL THEN
BEGIN
CALL {$spPInitialPostPublish}(aTopicId, aPostId, aUserId);
END;
END IF;
--
-- Re-publish the post it and remove the draft.
--
UPDATE {$tArticle} SET
titleArticle = aTitle,
contentArticle = aContent,
modifiedArticle = NOW(),
publishedArticle = NOW(),
draftTitleArticle = NULL,
draftContentArticle = NULL,
draftModifiedArticle = NULL
WHERE
idArticle = aPostId AND
{$udfFCheckUserIsOwnerOrAdmin}(aPostId, aUserId)
LIMIT 1;

END;
END IF;

--
-- Check some status issues, return as OUT parameters, might be useful in the GUI.
--
SELECT
IF(publishedArticle IS NULL, 0, 1),
IF(draftModifiedArticle IS NULL, 0, 1)
INTO
isPublished,
hasDraft
FROM {$tArticle}
WHERE
idArticle = aPostId
;

END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Insert some default topics
--
SET @action='publish';
SET @post=0;
SET @topic=0;
CALL {$spPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 1, 'Rome was not built in one day', 'At least, that is the common opinion.', @action);

SET @post=0;
CALL {$spPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 2, '', 'But you never now. I have heard otherwise.', @action);

SET @post=0;
SET @topic=0;
CALL {$spPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 2, 'A forum should be open for all', 'Everybody should be able to say what they feel.', @action);

SET @post=0;
CALL {$spPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 1, '', 'Is this really your opinion!!?', @action);

SET @post=0;
CALL {$spPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 2, '', 'No, just said it for the fun of it.', @action);

SET @post=0;
SET @topic=0;
CALL {$spPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 1, 'Which is the best forum ever?', 'I really would like to know your opinion on this matter.', @action);


EOD;


?>