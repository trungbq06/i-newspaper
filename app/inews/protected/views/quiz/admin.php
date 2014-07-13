<form id="search" method="post">
	<input type="text" name="name" id="keyword" value="<?php echo $keyword; ?>" style="width: 300px;" />
	<input type="radio" name="country" value="vn" checked />VN
	<input type="radio" name="country" value="us" <?php if ($country == 'us') echo 'checked'; ?> />US
	<input type="button" value="SEARCH" id="btn_search" />
</form>

<div id="result">
	<table cellspacing="0" cellpadding="0" id="search_table">
		<thead>
			<th>Song</th>			
			<th>Itunes URL</th>
			<th>Itunes Artist URL</th>
			<th>Preview URL</th>
			<th>Thumb</th>
			<th>Artist</th>
			<th>Country</th>
			<th>Genre</th>
			<th>Price</th>
			<th></th>
		</thead>
		
		<?php if (!empty($data)) : ?>
			<?php foreach ($data as $song) : ?>
				<td><?php echo $song->trackId; ?></td>
				<td><?php echo $song->trackName; ?></td>
				<td><?php echo $song->artistName; ?></td>
				<td><?php echo $song->trackViewUrl; ?></td>
				<td><?php echo $song->artistViewUrl; ?></td>
				<td><?php echo $song->previewUrl; ?></td>
				<td><?php echo $song->artworkUrl30; ?></td>
				<td><?php echo $song->country; ?></td>
				<td><?php echo $song->primaryGenreName; ?></td>
				<td><?php echo $song->trackPrice; ?></td>
				<td><input type="checkbox" name="t-<?php echo $song->trackId; ?>" /></td>
			<?php endforeach; ?>
		<?php else: ?>
			<td colspan="11" style="text-align: center;font-style:italic;">
				Sorry, no result match. Please try another song.
			</td>
		<?php endif; ?>
	</table>
</div>

<input type="text" id="level" />
<input type="button" name="submit" value="SAVE" id="submit" />

<script type="text/javascript">

$(function() {
	$('#btn_search').click(function() {
		doSearch();
	});
	
	$('#keyword').keypress(function(event) {
		if (event.keyCode == 13) {
			doSearch();
			event.preventDefault();
		}
	});
});

function doSearch()
{
	var keyword = $('#keyword').val();
	var user = '<?php echo $user; ?>';
	var pass = '<?php echo $pass; ?>';
	var country = $('input[name="country"]:checked').val();
	if (keyword && country) {
		$.ajax({
			url: '/quiz/admin',
			data: 'user=' + user + '&pass=' + pass + '&keyword=' + keyword + '&country=' + country,
			success: function(data) {
				$('#result').html(data);
			}
		});
	} else {
		alert('Whoops! You miss the song name ! :(');
	}
}

</script>






