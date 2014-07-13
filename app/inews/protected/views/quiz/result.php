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
		<?php $i = 0; foreach ($data as $song) : $i++;?>
		<tr <?php if ($i % 2==0) echo 'class="even"'; ?>>
			<td class="track_name"><b><?php echo $song->trackName; ?></b></td>			
			<td class="track_view_url"><a href="<?php echo $song->trackViewUrl; ?>"><?php echo $song->trackViewUrl; ?></a></td>
			<td class="artist_view_url"><?php echo isset($song->artistViewUrl) ? $song->artistViewUrl : ''; ?></td>
			<td>
				<input type="hidden" class="preview_url" value="<?php echo $song->previewUrl; ?>" />
				<audio controls width="50" preload="none">
					<source src="<?php echo $song->previewUrl; ?>" type="audio/mp4">
					Your browser does not support the audio element.
				</audio>
			</td>
			<td class="thumbnail"><img src="<?php echo $song->artworkUrl100; ?>" width="50" /></td>
			<td class="artist_name"><?php echo $song->artistName; ?></td>
			<td class="country"><?php echo $song->country; ?></td>
			<td class="genre_name"><?php echo $song->primaryGenreName; ?></td>
			<td class="track_price"><?php echo $song->trackPrice; ?></td>
			<td class="track_id"><input type="checkbox" name="t-<?php echo $song->trackId; ?>" /></td>
		</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<td colspan="11" style="text-align: center;font-style:italic;">
			Sorry, no result match. Please try another song.
		</td>
	<?php endif; ?>
</table>

<script type="text/javascript">

$(function() {
	$('table tr').click(function() {
		$('input[type=checkbox]').removeAttr('checked');
		
		$(this).find('input[type="checkbox"]').prop('checked', 'checked');
	});

	$('#submit').click(function() {
		var checkbox = $('input[type=checkbox]:checked');
		var tr = $(checkbox).closest('tr');
		
		var trackName = tr.find('.track_name').text();
		var itunesURL = encodeURIComponent(tr.find('.track_view_url').text());
		var artistURL = encodeURIComponent(tr.find('.artist_view_url').text());
		var previewURL = encodeURIComponent(tr.find('.preview_url').text());
		var thumbnail = tr.find('.thumbnail').text();
		var artistName = tr.find('.artist_name').text();
		var country = tr.find('.country').text();
		var genreName = tr.find('.genre_name').text();
		var trackPrice = tr.find('.track_price').text();
		
		var song = new Object();
		var source = new Object();
		source.itunes_url = itunesURL;
		source.artist_url = artistURL;
		source.preview_url = previewURL;
		source.thumbnail = thumbnail;
		source.artist_name = artistName;
		source.track_price = trackPrice;
		
		song.title = trackName;
		song.source = source;
		song.country = country;
		song.genre = genreName;
		song.level = $('#level').val();
		
		if ($.isNumeric(song.level)) {
			var data = new Array();
			data.push(song);
			
			$.ajax({
				url: '/quiz/insertSong',
				data: 'data=' + $.toJSON(data),
				success: function(data) {
					alert(data);
				}
			});
		} else {
			alert('Level is not valid');
		}
	});
});

</script>