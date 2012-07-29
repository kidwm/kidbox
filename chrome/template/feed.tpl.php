<rss version="2.0">
	<channel>
		<title><?php echo APP_NAME; ?></title>
		<description><?php echo APP_NAME; ?></description>
		<link><?php echo $prefix.OUT_PATH; ?></link>
<?php foreach ($row as $list): ?>
		<item>
			<title><?php echo $list['post_title']; ?></title>
			<link><?php echo $prefix.OUT_PATH.$list['id']; ?></link>
			<description>
<?php echo indent_text($list['post_content'], 4); ?>
			</description>
			<author><?php echo $list['post_author']; ?></author>
			<category><?php echo $list['post_board_nicename']; ?></category>
			<comments><?php echo $prefix.OUT_PATH.$list['id'].'#comment'; ?></comments>
			<guid><?php echo $prefix.OUT_PATH.$list['id']; ?></guid>
			<pubDate><?php echo date(DATE_RSS, strtotime($list['post_date'])); ?></pubDate>
		</item>
<?php endforeach; ?>
	</channel>
</rss>
