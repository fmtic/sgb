document.addEventListener("DOMContentLoaded", function () {
    const isbnInput = document.getElementById("isbn");
    const capaInput = document.getElementById("capa");
    const capaPreview = document.getElementById("capa-preview");
  
    // Validação em tempo real
    document.querySelectorAll("input, textarea, select").forEach((element) => {
      element.addEventListener("input", () => {
        if (element.checkValidity()) {
          element.classList.remove("invalido");
          element.classList.add("valido");
        } else {
          element.classList.remove("valido");
          element.classList.add("invalido");
        }
      });
    });
  
    // Preencher dados automaticamente via ISBN
    isbnInput.addEventListener("blur", function () {
      const isbn = isbnInput.value.replace(/[^0-9X]/gi, "");
      if (isbn.length >= 10) {
        fetch(`https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`)
          .then((res) => res.json())
          .then((data) => {
            if (data.totalItems > 0) {
              const book = data.items[0].volumeInfo;
              document.getElementById("titulo").value = book.title || "";
              document.getElementById("autor").value = (book.authors || []).join(", ");
              document.getElementById("editora").value = book.publisher || "";
              document.getElementById("ano").value = book.publishedDate ? book.publishedDate.substring(0, 4) : "";
              document.getElementById("genero").value = (book.categories || []).join(", ");
              document.getElementById("idioma").value = book.language || "";
              document.getElementById("sinopse").value = book.description || "";
              document.getElementById("paginas").value = book.pageCount || "";
  
              if (book.imageLinks && book.imageLinks.thumbnail) {
                document.getElementById("capa-url").value = book.imageLinks.thumbnail;
                capaPreview.src = book.imageLinks.thumbnail;
                capaPreview.style.display = "block";
              }
            } else {
              alert("Livro não encontrado via ISBN.");
            }
          })
          .catch(() => alert("Erro ao buscar dados do livro."));
      }
    });
  
    // Preview da imagem de capa (upload manual)
    capaInput.addEventListener("change", function () {
      const file = capaInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          capaPreview.src = e.target.result;
          capaPreview.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  });
  