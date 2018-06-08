var popup = function(t) {
  alert("The author is "+t.dataset.author);
}

var authorLink = document.getElementById('afqAuthorLink');
authorLink.addEventListener('click', e => popup(e.target));