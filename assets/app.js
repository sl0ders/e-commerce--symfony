import './styles/app.scss'
import $ from "../public/Mdb5/js/jquery"

$('.cross').on('click', () => {
    console.log('close');
});

$(document).on('click', '.edit-stock', function () {
    event.preventDefault();
    const id = $(this).data('id')
    let value = $("#" + id).val()
    let path = $(this).attr("data-path")
    let stock = $('.stock-' + id)
    stock.html(value)
    $.ajax({
        url: path,
        type: "POST",
        data: {value},
        dataType: 'json',
        success: (data) => {
            console.log(data)
        },
        error: (data) => {
            console.log("Erreur" + data)
        }
    })
})

class Autogrow extends HTMLTextAreaElement {

    constructor() {
        super();
        this.onFocus = this.onFocus.bind(this);
        this.autogrow = this.autogrow.bind(this);
    }

    /**
     * Executer quand l'element est ajouter au DOM
     */
    connectedCallback() {
        this.addEventListener('focus', this.onFocus);
        this.addEventListener('input', this.autogrow);
    }

    /**
     * Appeler quand l'element sera supprimer
     */
    disconnectedCallback() {

    }

    onFocus() {
        this.autogrow();
        this.removeEventListener('focus', this.onFocus)
    }

    autogrow() {
        this.style.height = 'auto';
        this.style.overflow = 'hidden';
        this.style.height = this.scrollHeight + 'px';
    }
}

customElements.define('textarea-autogrow', Autogrow, {extends: 'textarea'});
