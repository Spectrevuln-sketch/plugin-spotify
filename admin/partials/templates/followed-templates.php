<div class="flex flex-1">
  <div class="flex flex-1 justify-between items-center gap-4">
    <img src="${res.current_artist.images[0].url}" class="w-14 h-14" />
    <div class="flex flex-1 flex-col">
      <h1 class="sm:text-md lg:text-xl font-bold">${res.current_artist.name}</h1>
      <p>${res.current_artist.followers.total} Followers</p>
    </div>
    <img src="${res.album_artist[0].images[0].url}" class="w-14 h-14" />
  </div>
  <div class="flex flex-1 flex-row">
    <h1>Top Track</h1>
    <div class="grid lg:grid-cols-4 sm:grid-cols-2">
      ${res.top_track_artist.map(item => {
      return `
      <img src="${item.album.images[0].url}" class="w-24 h-24" />
      <p>${item.name}</p>
      `
      }).join('')}
    </div>
  </div>
</div>