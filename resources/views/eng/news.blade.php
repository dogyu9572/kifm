@extends('layouts.frontend')
@section('title', $geName)
@section('gName', $gName)
@section('sName', $geName)
@section('content')
<main class="sub_area">

<section class="scon" aria-labelledby="society-notices-heading">
	<div class="inner">
		<h1 class="sub_title" id="society-notices-heading">{{ $geName }}</h1>

		<div class="board_top">
			<div class="left">
				<div class="total">Total <strong class="c_iden">100</strong></div>
			</div>
			<div class="right flex">
				<select name="" id="" class="text">
					<option value="">All</option>
					<option value="">Title</option>
					<option value="">Content</option>
				</select>
				<form class="search_area">
					<label for="event-search" class="sound_only">Search by title</label>
					<input type="text" id="event-search" class="text" placeholder="Enter title">
					<button type="submit" class="btn_search">Search</button>
				</form>
			</div>
		</div>
		
		<div class="board_list">
			<table>
				<caption>Notice board for Korean Society for Functional Medicine.</caption>
				<colgroup>
					<col class="num">
					<col>
					<col class="date">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO.</th>
						<th scope="col">Title</th>
						<th scope="col">Date</th>
					</tr>
				</thead>
				<tbody>
					<tr class="notice">
						<td class="num" aria-label="Notice"></td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr class="notice">
						<td class="num" aria-label="Notice"></td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">8</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">7</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">6</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">5</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">4</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">3</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">2</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
					<tr>
						<td class="num">1</td>
						<td class="tal"><a href="/eng/news_view">This is where the title will be placed. This is where the title will be placed.</a></td>
						<td class="date">2025.01.01</td>
					</tr>
				</tbody>
			</table>
		</div>

		<nav class="board-pagination" aria-label="Board page navigation">
			<ul class="pagination">
				<li class="page-item arw_item"><a class="page-link" href="#" title="First page" aria-label="Go to first page"><i class="arrow two first" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="Previous page" aria-label="Go to previous page"><i class="arrow one prev" aria-hidden="true"></i></a></li>
				<li class="page-item active"><span class="page-link" aria-current="page" aria-label="Current page 1">1</span></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 2">2</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 3">3</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 4">4</a></li>
				<li class="page-item"><a class="page-link" href="#" aria-label="Go to page 5">5</a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="Next page" aria-label="Go to next page"><i class="arrow one next" aria-hidden="true"></i></a></li>
				<li class="page-item arw_item"><a class="page-link" href="#" title="Last page" aria-label="Go to last page"><i class="arrow two last" aria-hidden="true"></i></a></li>
			</ul>
		</nav>
		
	</div>
</section>
	
</main>

@endsection