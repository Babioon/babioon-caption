<?php
/**
 * Babioon-Caption
 * @author     Robert Deutz <rdeutz@googlemail.com>
 * @package    BABIOONCAPTION
 *
 * @copyright  Robert Deutz
 * @license    GNU General Public License version 2 or later
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgContentBabiooncaption extends JPlugin
{

    /**
   	 * Adding the caption to the text
   	 *
   	 * @param	string	The context of the content being passed to the plugin.
   	 * @param	object	The article object.  Note $article->text is also available
   	 * @param	object	The article params	
   	 * @param	int		The 'page' number
   	 */
	public function onContentPrepare($context, $article, $params, $page = 0)
    {
		
		preg_match_all('#<img[^>]*>#i', $article->text, $match);
		$replace = $match[0];
		$doreplace=false;

		if(count($replace))
		{
			$search_str = array();
			$replace_str = array();

			foreach ($replace as $elm)
			{
				preg_match_all('/(alt|title|class|width|height|style|src)=("[^"]*")/i',$elm, $matchtags);

				if(count($matchtags[1]) != 0)
				{
					$pos 	= array_search('class', $matchtags[1]);
					$class 	=  trim($pos !== false ? $matchtags[2][$pos] : '','"');
					
					if (strpos($class,'caption') !== false)
					{
						$tags = array('alt','title','width','height','style','src','class');
						$img = new stdClass();						

						foreach($tags as $tag)
						{
							$pos 		= array_search($tag, $matchtags[1]);
							$img->$tag 	= trim($pos !== false ? $matchtags[2][$pos] : '','"'); 	
						}
						$caption = '';

						if (trim($img->title) != '')
						{
							$caption = $img->title;
						}
						elseif (trim($img->alt) != '')
						{
							$caption = $img->alt;
						}

						$left = false;
						$right = false;
						$float = '';

						if ($caption != '')
						{
							// check left or right, find a float 
							$pos = strpos($img->style,'float');

							if ($pos !== false)
							{
								// look what float:
								$pos2 = (int) strpos ($img->style,';',$pos);

								if ( $pos2 != 0)
								{
									$pos2 = $pos2-$pos;
								}
								$tmp = substr($img->style,$pos, $pos2);
								$right = strpos($tmp,'right') !== false;
								$left = strpos($tmp,'left') !== false;

								if ($left)
								{
									$float = 'float: left';
								}	
								elseif ($right)
								{
									$float = 'float: right';
								}	
							}
							$ntag = '<div class="img_caption';
							$ntag .= $left ? ' left' : '';							
							$ntag .= $right ? ' right' : '';
							$ntag .= '" style="';
							$ntag .= $left ? 'float: left;' : '';							
							$ntag .= $right ? 'float: right;' : '';

							if ($img->width != '')
							{
								$ntag .= 'width: '.$img->width.'px;';
							} 
							$ntag .= '">';
							$ntag .= '<img';
							//$ntag .= ' class="'.$img->class.'"';
							
							if ($img->width != '') 
							{
								$ntag .= ' width="'.$img->width.'"';
							}

							if ($img->height != '')
							{
								$ntag .= ' height="'.$img->height.'"';
							} 
							$ntag .= ' src="'.$img->src.'"';

							if ($img->alt != '') 
							{
								$ntag .= ' alt="'.$img->alt.'"';
							}

							if ($img->title != '')
							{
								$ntag .= ' title="'.$img->title.'"';
							}

							if ($img->style != '')
							{
								$ntag .= ' style="'.$img->style.'"';
							} 
							$ntag .= ' />';
							$ntag .= '<p class="img_caption">'.$caption.'</p>';
							$ntag .= '</div>';
							
							$search_str[]	= $elm;
							$replace_str[] 	= $ntag;
							$doreplace=true;
						}
					}	
				}
			}
			if ($doreplace)
			{
				$article->text=str_replace($search_str, $replace_str, $article->text);
			}
		}
    	return true;
    }
}
/* EOF */