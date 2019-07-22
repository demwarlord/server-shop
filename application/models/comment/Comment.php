<?php

/**
* Comment model
*
* @author dev. Dmitry Kamyshov <dk@company.com>
* @package Model
* @version 2.0.0
*/

namespace Models {

    class Comment
    {

        /**
        * Add new server comment.
        *
        * @param
        *
        * Array
        * (
        *     [server_id] => int(11)
        *     [author]    => varchar(100)
        *     [email]     => varchar(100)
        *     [comment]   => text
        *     [rating]    => int(11)
        *     [author_id] => bigint(11)
        *     [last_id]   => int(11)
        * )
        *
        * @return array|false
        */
        public function addServerComment($addCommentArray)
        {
            if (!empty($addCommentArray)) {
                $sql = "INSERT INTO `server_comments` "
                     . "(`server_id`, `author`, `email`, `comment`, `rating`, `author_id`, `last_id`) "
                     . "VALUES ( "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['serverId']) . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['author'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['email'])    . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['comment'])  . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['rating'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['userId'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['lastId'])   . "'"
                     . ") ";
                return \Models\Db::getInstance()->insert($sql);
            } else {
                return false;
            };
        }

        /**
        * Add new server reply.
        *
        * @param
        *
        * Array
        * (
        *     [server_id] => int(11)
        *     [author]    => varchar(100)
        *     [email]     => varchar(100)
        *     [comment]   => text
        *     [parent]    => int(11)
        *     [rating]    => int(11)
        *     [author_id] => bigint(11)
        *     [last_id]   => int(11)
        * )
        *
        * @return array|false
        */
        public function addServerCommentReply($addCommentArray)
        {
            if (!empty($addCommentArray)) {
                $sql = "INSERT INTO `server_comments` "
                     . "(`server_id`, `author`, `email`, `comment`, `parent`, `rating`, `author_id`, `last_id`) "
                     . "VALUES ( "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['serverId']) . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['author'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['email'])    . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['comment'])  . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['parent'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['rating'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['userId'])   . "', "
                     . "'" . \Models\Db::getInstance()->escapeData($addCommentArray['lastId'])   . "'"
                     . ") ";
                return \Models\Db::getInstance()->insert($sql);
            } else {
                return false;
            };
        }

        /**
        * Getting comments by server id.
        *
        * @param int $serverId
        *
        * @return array|false
        */
        public function getCommentsByServerId($serverId, $sort = "DESC")
        {
            $sql = "SELECT * "
                 . "FROM `server_comments` "
                 . "WHERE `server_id` = '" . \Models\Db::getInstance()->escapeData($serverId) . "' "
                 . "AND `validated` = 1 ";

            if ($sort === "DESC") {
                $sql .= "ORDER BY `creation_time` DESC";
            } else if ($sort === "ASC") {
                $sql .= "ORDER BY `creation_time` ASC";
            }

            return \Models\Db::getInstance()->getAll($sql);
        }

        /**
        * Getting server rating by server id.
        *
        * @param int $serverId
        *
        * @return array|false
        */
        public function getServerRating($serverId)
        {
            $sql = "SELECT ROUND(SUM(`rating`)/COUNT(*),1) AS 'server_rating' "
                 . "FROM `server_comments` "
                 . "WHERE `server_id` = '" . \Models\Db::getInstance()->escapeData($serverId) .  "' "
                 . "AND `validated` = 1 "
                 . "AND `parent` = 0";

            return \Models\Db::getInstance()->getOneRowAssoc($sql);
        }

        public function notifyOnComment($comment, $page)
        {
            $api = \Models\Api::getInstance();
            $smarty_mail = new \System\SmartyMail();

            $smarty_mail->From = EML_NOREPLY;
            $smarty_mail->FromName = "company.com - Notify";
            $smarty_mail->AddAddress(EML_MODERATOR);
            $smarty_mail->Subject = "company.com - New comment has arrived on page: " . $page;
            $sent_status = $smarty_mail->SendSmartyLetter(['comment' => print_r($comment, true), 'page' => $page], 'comment_notify_email.tpl', false);

            $report = [
                'type'        => 'email',
                'timestamp'   => time(),
                'sent_status' => $sent_status ? 1 : 0,
                'data'        => [
                    'subject'      => $smarty_mail->Subject,
                    'to'           => EML_MODERATOR,
                    'from'         => $smarty_mail->From,
                    'content-type' => 'html',
                    'content'      => $smarty_mail->Body
                ]
            ];

            $api->notification('insertNotificationEvent', ['data' => $report]);
            return;
        }

        /**
        * Gets last comments by rating and limit
        *
        * @param int $limit
        * @return array|false
        */
        public function getLastComments($limit = 3)
        {
            if (!empty($limit) && is_int($limit)) {
                $sql = "SELECT `cmm`.`server_id`, "
                        . "`cmm`.`comment`, "
                        . "`cmm`.`author`, "
                        . "`cmm`.`creation_time`, "
                        . "`cmm`.`rating`, "
                        . "`cmm`.`email`, "
                        . "`srv`.`url` "
                     . "FROM `server_comments` `cmm` "
                     . "JOIN `servers` `srv` "
                        . "ON (`srv`.`id` = `cmm`.`server_id`) "
                     . "WHERE `cmm`.`validated` = 1 "
                     . "ORDER BY `cmm`.`rating` DESC, "
                        . "`cmm`.`comment_id` ASC "
                     . "LIMIT " . $limit;

                return \Models\Db::getInstance()->getAll($sql);
            } else {
                return false;
            }
        }

        /**
         * Increases the vote counter on the given table and comment
         *
         * @param int $id
         * @param int $yesno
         * @param int $table
         * @return int|bool
         */
        public function voteComment($id, $yesno, $table) {
            if (!empty($id) && is_int($id) && in_array($yesno,[0,1]) && in_array($table,[1,2,3])) {
                if ($yesno) {
                    $field = 'useful_yes';
                } else {
                    $field = 'useful_no';
                }
                switch ($table) {
                    case 1:
                        $table = 'server_comments';
                        break;
                    case 2:
                        $table = 'blog_comments';
                        break;
                    case 3:
                        $table = 'faq_comments';
                        break;
                    default:
                        $table = 'server_comments';
                        break;
                }
                $sql = "UPDATE `" . $table . "` "
                        . "SET `" . $field . "` = `" . $field . "` + 1 "
                        . "WHERE `comment_id` = '" . $id . "'";
                \Models\Db::getInstance()->update($sql);

                $sql = "SELECT `" . $field . "` FROM `" . $table . "` "
                        . "WHERE `comment_id` = '" . $id . "'";
                return \Models\Db::getInstance()->getOne($sql);
            } else {
                return false;
            }
        }

    }
}
