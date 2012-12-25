<?php

/* ---------------------------------------------------------------------------
 * @Plugin Name: Treeblogs
 * @Plugin Id: Treeblogs
 * @Plugin URI:
 * @Description: Дерево блогов
 * @Author: mackovey@gmail.com
 * @Author URI: http://stfalcon.com
 * @LiveStreet Version: 0.4.2
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * ----------------------------------------------------------------------------
 */

/**
 * Маппер Blog модуля Blog плагина Treeblogs
 */
class PluginTreeblogs_ModuleBlog_MapperBlog extends PluginTreeblogs_Inherit_ModuleBlog_MapperBlog
{

    /**
     * Возвращаем родительский блог
     *
     * @param ModuleBlog_EntityBlog $oBlog
     * @return int BlogId
     */
    public function getParentBlogId($blog_id)
    {
        $sql = "
		SELECT b.parent_id
		  FROM " . Config::Get('db.table.blog') . " as b
		 WHERE b.blog_id = ?
		";
        $aRows = $this->oDb->select($sql, $blog_id);
        if (isset($aRows[0])) {
            return $aRows[0]['parent_id'];
        }
        return null;
    }

    /**
     * Обновление данные блога по treeblog
     *
     * @param ModuleBlog_EntityBlog $oBlog
     * @return boolean
     */
    public function UpdateTreeblogData($oBlog)
    {
        $sql = "UPDATE
                    " . Config::Get('db.table.blog') . "
                SET
                    parent_id = ?,
                    order_num = ?,
                    blogs_only = ?
                WHERE
                    blog_id = ?d
		";
        $this->oDb->query($sql, $oBlog->getParentId(), $oBlog->getOrderNum(), $oBlog->getBlogsOnly(), $oBlog->getId());
        return true;
    }

    /**
     * Получаем дочерние блоги
     * @param BlogId
     * @return array
     */
    public function GetSubBlogs($iBlogId, &$iCount, $iCurrPage , $iPerPage)
    {
        $sql = "SELECT
                    b.blog_id
                FROM
                    " . Config::Get('db.table.blog') . " as b
                WHERE
                    b.parent_id = ?
                ORDER BY
                    b.blog_rating DESC
                LIMIT
                    ?d, ?d;
                ";

        $aResults = array();

        if ($aRows = $this->oDb->selectPage($iCount, $sql, $iBlogId, ($iCurrPage - 1) * $iPerPage, $iPerPage)) {
            foreach ($aRows as $aBlog) {
                $aResults[] = $aBlog['blog_id'];
            }
        }
        return $aResults;
    }

    public function GetAllSubBlogs($iBlogId)
    {
        $sql = "SELECT
                    b.blog_id
                FROM
                    " . Config::Get('db.table.blog') . " as b
                WHERE
                    b.parent_id = ?
                ORDER BY
                    b.blog_rating DESC
                ";
        $aBlogs = array();
        if ($aRows = $this->oDb->select($sql, $iBlogId)) {
            foreach ($aRows as $aBlog) {
                $aBlogs[] = $aBlog['blog_id'];
            }
        }
        return $aBlogs;
    }

    public function GetCountSubBlogs($iBlogId)
    {
        $sql = "SELECT
                    COUNT(b.blog_id) as count
                FROM
                    " . Config::Get('db.table.blog') . " as b
                WHERE
                    b.parent_id = ?
                ";
        if ($aRow=$this->oDb->selectRow($sql, $iBlogId)) {
			return $aRow['count'];
		}
		return false;
    }

    /**
     * Выбираем блоги для меню
     * @param int $iUserOwnerId
     * @return array
     */
    public function GetMenuBlogs($iUserOwnerId = 0)
    {
        $sql = "";
        $aRows = array();
        if ($iUserOwnerId == 0) {
            $sql = "
                    SELECT b.blog_id
                      FROM " . Config::Get('db.table.blog') . " as b
                     WHERE b.parent_id IS NULL
                       AND b.blog_type <> 'personal'
                       AND b.blog_type = 'open'
                     ORDER BY b.order_num DESC
                    ";
            $aRows = $this->oDb->select($sql);
        } else {
            $sql = "
                    SELECT 2 main, b.blog_id
                      FROM " . Config::Get('db.table.blog') . " as b
                     WHERE b.blog_type = 'open' AND b.parent_id IS NULL
                     UNION ALL
                    SELECT 1 main, b.blog_id
                      FROM " . Config::Get('db.table.blog') . " as b
                     WHERE b.blog_type = 'personal' AND b.user_owner_id = ?d
                     ORDER BY main ASC
                    ";
            $aRows = $this->oDb->select($sql, $iUserOwnerId);
        }
        $aBlogs = array();
        if ($aRows) {
            foreach ($aRows as $aBlog) {
                $aBlogs[] = $aBlog['blog_id'];
            }
        }
        return $aBlogs;
    }

    /**
     * Возвращаем блоги для выбора
     *
     * @param int|null $iBlogId
     * @return \ModuleBlog_EntityBlog
     */
    public function GetBlogsForSelect($iBlogId = null)
    {
        $sql = "SELECT
                    *
                FROM
                    " . Config::Get('db.table.blog') . " as b
                WHERE
                    b.blog_type<>'personal'
                  AND
                    b.blog_type = 'open'

                ";

        if ((int) $iBlogId) {
            $sql .= 'AND b.blog_id != ' . $iBlogId;
        }
        $sql .= " ORDER BY b.blog_title";

        $aBlogs = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aBlogs[] = Engine::GetEntity('Blog', $aRow);
            }
        }
        return $aBlogs;
    }

    public function GetBlogRelations()
    {
        $sql = "
		SELECT b.blog_id, b.parent_id
		  FROM " . Config::Get('db.table.blog') . " as b
		 WHERE b.blog_type<>'personal' AND b.blog_type = 'open'
		";
        $aBlogs = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aBlogs[$aRow['blog_id']] = $aRow['parent_id'];
            }
        }
        return $aBlogs;
    }

    /**
     * Get blog ids for blog only
     *
     * @return array()
     */
    public function GetBlogOnlyBlogs()
    {
        $sql = "SELECT
                    blog_id
                FROM
                    " . Config::Get('db.table.blog') . "
                WHERE
                    blogs_only = 1
                ";
        $aBlogs = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aBlogs[] = $aRow['blog_id'];
            }
        }
        return $aBlogs;
    }

    public function GetBlogs()
    {
        $sql = "SELECT
                    b.blog_id
                FROM
                    " . Config::Get('db.table.blog') . " as b
                WHERE
                    b.blog_type<>'personal'
                AND blogs_only = 0
				";
        $aBlogs = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aBlog) {
                $aBlogs[] = $aBlog['blog_id'];
            }
        }
        return $aBlogs;
    }

    public function GetBlogsRating(&$iCount, $iCurrPage, $iPerPage)
    {
        $sql = "SELECT
					b.blog_id
				FROM
					" . Config::Get('db.table.blog') . " as b
				WHERE
					b.blog_type<>'personal'
                AND blogs_only = 0
				ORDER by b.blog_rating desc
				LIMIT ?d, ?d 	";
        $aReturn = array();
        if ($aRows = $this->oDb->selectPage($iCount, $sql, ($iCurrPage - 1) * $iPerPage, $iPerPage)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = $aRow['blog_id'];
            }
        }
        return $aReturn;
    }

    public function GetBlogsRatingJoin($sUserId, $iLimit)
    {
        $sql = "SELECT
					b.*
				FROM
					" . Config::Get('db.table.blog_user') . " as bu,
					" . Config::Get('db.table.blog') . " as b
				WHERE
					bu.user_id = ?d
					AND
					bu.blog_id = b.blog_id
					AND
					b.blog_type<>'personal'
                AND blogs_only = 0
				ORDER by b.blog_rating desc
				LIMIT 0, ?d
				;
					";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql, $sUserId, $iLimit)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = Engine::GetEntity('Blog', $aRow);
            }
        }
        return $aReturn;
    }

    public function GetBlogsRatingSelf($sUserId, $iLimit)
    {
        $sql = "SELECT
					b.*
				FROM
					" . Config::Get('db.table.blog') . " as b
				WHERE
					b.user_owner_id = ?d
					AND
					b.blog_type<>'personal'
                AND blogs_only = 0
				ORDER by b.blog_rating desc
				LIMIT 0, ?d
			;";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql, $sUserId, $iLimit)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = Engine::GetEntity('Blog', $aRow);
            }
        }
        return $aReturn;
    }

    public function GetCloseBlogs()
    {
        $sql = "SELECT b.blog_id
				FROM " . Config::Get('db.table.blog') . " as b
				WHERE b.blog_type='close'
                AND blogs_only = 0
			;";
        $aReturn = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aReturn[] = $aRow['blog_id'];
            }
        }
        return $aReturn;
    }

}
