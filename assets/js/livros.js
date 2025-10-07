// Preview da capa
document.getElementById('capaInput').addEventListener('change', function(event){
    const preview = document.getElementById('imgPreview');
    const file = event.target.files[0];

    if(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
});


// Validação do ISBN e consulta via Google Books
const inputISBN = document.querySelector('input[name="isbn"]');

inputISBN.addEventListener('blur', function() {
    const isbn = this.value.trim();
    if (isbn.length === 10 || isbn.length === 13) {
        fetch(`https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`)
            .then(response => response.json())
            .then(data => {
                if (data.totalItems > 0) {
                    const book = data.items[0].volumeInfo;
                    document.querySelector('input[name="titulo"]').value = book.title || '';
                    document.querySelector('input[name="autor"]').value = (book.authors || []).join(', ');
                    document.querySelector('input[name="editora"]').value = book.publisher || '';
                    document.querySelector('input[name="ano"]').value = book.publishedDate ? book.publishedDate.substring(0,4) : '';
                    document.querySelector('input[name="genero"]').value = (book.categories || []).join(', ');
                } else {
                    console.log('ISBN não encontrado na Google Books');
                }
            })
            .catch(err => console.error('Erro ao consultar Google Books:', err));
    } else {
        console.log('ISBN inválido (deve ter 10 ou 13 caracteres)');
    }
});
