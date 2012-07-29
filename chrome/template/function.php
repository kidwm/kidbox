<?php
function page_list($current_page = 0, $total_pages = 0, $page_range = 5, $link, $page_argument, $anchor = '') {
	$items_pages = '';
	$page_argument = $page_argument.'=';
	$anchor = empty($anchor) ? '' : '#'.$anchor;
	$prefix = $current_page + $page_range > $total_pages ? $current_page + $page_range - $total_pages: 0;
	$postfix = $current_page - $page_range <= 0 ? $page_range - $current_page: 0;
	$start = $current_page - $page_range - $prefix;
	$end = $current_page + $page_range + $postfix;
	if ($current_page > 1) {
		$items_pages .= '<span class="pages-items first-page"><a href="'.$link.$page_argument.'1'.$anchor.'" title="最前頁">&lt;&lt;</a></span>'."\n";
		$prev_page = $current_page - 1;
		$items_pages .= '<span class="pages-items prev-page"><a href="'.$link.$page_argument.$prev_page.$anchor.'" title="前一頁">&lt;</a></span>'."\n";
		}
	else {
		$items_pages .= '<span class="pages-items dummy-page">&lt;&lt;</span>'."\n";
		$items_pages .= '<span class="pages-items dummy-page">&lt;</span>'."\n";
	}
	for ($i = $start; $i <= $end; $i++) {
		if (($i > 0) && ($i <= $total_pages)) { 
			if ($i == $current_page)
				$items_pages .= '<span class="pages-items current-page">'.$i.'</span>'."\n";
			else
				$items_pages .= '<span class="pages-items"><a href="'.$link.$page_argument.$i.$anchor.'" title="第'.$i.'頁">'.$i.'</a></span>'."\n";
		}
		elseif ($i == 0 && $total_pages == 0)
			$items_pages .= '<span class="pages-items dummy-page">0</span>'."\n";
	}
	if ($current_page != $total_pages) {  
		$next_page = $current_page + 1;  
		$items_pages .= '<span class="pages-items next-page"><a href="'.$link.$page_argument.$next_page.$anchor.'" title="後一頁">&gt;</a></span>'."\n"; 
		$items_pages .= '<span class="pages-items last-page"><a href="'.$link.$page_argument.$total_pages.$anchor.'" title="最後頁">&gt;&gt;</a></span>';
	}
	else {
		$items_pages .= '<span class="pages-items dummy-page">&gt;</span>'."\n"; 
		$items_pages .= '<span class="pages-items dummy-page">&gt;&gt;</span>';
	}
	return $items_pages;
}

function member_link($member) {
	if (is_array($member)) {
		$link = '<a href="'.OUT_PATH.'member/'.$member['login'].'" title="'.$member['login'].'">'.$member['nicename'].'</a>';
	}
	else {
		$who = member_who($member);
		$link = '<a href="'.OUT_PATH.'member/'.$who['login'].'" title="'.$who['login'].'">'.$who['nicename'].'</a>';
	}
	return $link;
}

function get_avatar($mail,$size = 80) {
	return ('http://www.gravatar.com/avatar/'.md5($mail).'?d='.urlencode(get_stuff('nobody.png')).'&amp;s='.$size);
}

function daytime() {
	if (date('G',time()) >= 19 || date('G',time()) <= 7)
		return 'night';
	else
		return 'day';
}