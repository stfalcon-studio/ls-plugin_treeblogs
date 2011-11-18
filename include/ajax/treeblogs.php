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

set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$sDirRoot=dirname(dirname(dirname(dirname(dirname(__FILE__)))))	;
require_once($sDirRoot."/config/config.ajax.php");

$sText = "";
$noValue=true;
if ($oEngine->User_IsAuthorization()) 
{
	$action = getRequest('action',null,'post');
	/*Запрос на создание новой группы*/
	if ($action=="newgroup") 
	{
		$groupIdx=getRequest('groupIdx',null,'post');
		$oEngine->Viewer_Assign('groupIdx',$groupIdx);
		$sText = $oEngine->Viewer_Fetch(Plugin::GetTemplatePath('treeblogs') . 'actions/ActionTopic/empty_group.tpl');
		$noValue=false;
	}
	/*Запрос на получение "родніх братьев" или "потомков"*/
	if ($action=="level" || $action=="children" ) 
	{
		$sBlogId=getRequest('blogid',null,'post');

		$nextlevel=getRequest('nextlevel',null,'post');
		$groupid=getRequest('groupid',null,'post');

		$oEngine->Viewer_Assign('groupid', $groupid );
		$oEngine->Viewer_Assign('nextlevel', $nextlevel );

		/*возвращаем соседние элементы для BlogId*/
		if ($action=="level")
		{
			if ($sBlogId==-1) 
			{ /*запрос на возврат корня дерева*/
				$aBlogs  = $oEngine->Blog_GetMenuBlogs($sBlogId);
			} else 
			{ /*Запрос на возврат уровня дерева*/
				$aBlogs  = $oEngine->Blog_GetSibling ($sBlogId);
			}

			if (count($aBlogs)>0)
			{
				$aoBlogs = $oEngine->Blog_GetBlogsAdditionalData ($aBlogs);
				$parentId = $aoBlogs[$aBlogs[0]]->getParentId();

				$oEngine->Viewer_VarAssign();
				$oEngine->Viewer_Assign('BlogId',$sBlogId);
				$oEngine->Viewer_Assign('aBlogs',$aoBlogs);
				$oEngine->Viewer_Assign('ParentId', $parentId );
				$sText = $oEngine->Viewer_Fetch(Plugin::GetTemplatePath('treeblogs') . 'actions/ActionTopic/select_blogs.tpl');
				$noValue=false;
			}
		}
		/*возвращаем дочерние элементы для BlogId*/
		if ($action=="children")
		{
			$sBlogId=getRequest('blogid',null,'post');
			$aBlogs  = $oEngine->Blog_GetSubBlogs($sBlogId);
			if (count($aBlogs)>0)
			{
				$aoBlogs = $oEngine->Blog_GetBlogsAdditionalData ($aBlogs);
				$parentId = $aoBlogs[ $aBlogs[0] ]->getParentId();
					
				$oEngine->Viewer_VarAssign();
				$oEngine->Viewer_Assign('BlogId',$sBlogId);
				$oEngine->Viewer_Assign('aBlogs',$aoBlogs);
				$oEngine->Viewer_Assign('ParentId',$parentId);
				$sText = $oEngine->Viewer_Fetch(Plugin::GetTemplatePath('treeblogs') . 'actions/ActionTopic/select_blogs.tpl');
				$noValue=false;
			}
		}
	}
}

$GLOBALS['_RESULT'] = array(
"noValue"     => $noValue,
"select"   => $sText,
);

