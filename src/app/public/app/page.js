/*jshint unused: false */
class Pagination {
    constructor(element, options = {}) {
        this.element = element;
        this.options = Object.assign({}, {
            current: 1,
            total: 1,
            size: 15,
            onPageChanged: () => {
            }
        }, options);

        this.previousText = element.dataset.previous;
        this.nextText = element.dataset.next;
    }

    render() {
        const {current, total, size} = this.options;
        const pages = [];
        const totalPages = Math.ceil(total / size);
        const start = Math.max(1, current - 3);
        const end = Math.min(totalPages, current + 3);
        if (current > 1) {
            pages.push(this.createLink(current - 1, this.previousText, 'page-link', 'aria-label', this.previousText));
        } else {
            pages.push(this.createLink(null, this.previousText, 'page-link disabled', 'aria-label', this.previousText));
        }

        if (start > 1) {
            pages.push(this.createLink(1, '1', 'page-link', 'aria-current', 1 === current ? 'page' : null));
            if (start > 2) {
                pages.push(this.createLink(current - 5, '...', 'page-link', 'aria-label', 'More Pages'));
            }
        }

        for (let i = start; i <= end; i++) {
            pages.push(this.createLink(i, i, 'page-link', 'aria-current', i === current ? 'page' : null));
        }

        if (end < totalPages) {
            if (end < totalPages - 1) {
                pages.push(this.createLink(current + 5, '...', 'page-link', 'aria-label', 'More Pages'));
            }
            pages.push(this.createLink(totalPages, totalPages, 'page-link', 'aria-current', totalPages === current ? 'page' : null));
        }

        if (current < totalPages) {
            pages.push(this.createLink(current + 1, this.nextText, 'page-link', 'aria-label', this.nextText));
        } else {
            pages.push(this.createLink(null, this.nextText, 'page-link disabled', 'aria-label', this.nextText));
        }

        this.element.innerHTML = `
    <nav aria-label="Page navigation">
      <ul class="pagination  justify-content-center">
        ${pages.join('')}
      </ul>
    </nav>
  `;

        const links = this.element.querySelectorAll('.page-link:not(.disabled)');
        links.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const target = event.currentTarget;
                const page = target.getAttribute('data-page');
                this.options.current = parseInt(page, 10);
                this.render();
                this.options.onPageChanged(this.options.current);
            });
        });
    }


    /*jshint ignore:start*/
    createLink(page, text, classes = 'page-link', attribute = null, attributeValue = null) {
        var add = "";
        if (attribute && attributeValue) {
            add = attribute + '="' + attributeValue + '"'

        }
        if (this.options.current === page) {
            classes += ' active';
        }

        return `<li class="page-item"><a href="javascript:void(0)" class="${classes}" data-page="${page}" ${add}>${text}</a></li>`;
    }

    /*jshint ignore:end*/
    update(options) {
        this.options = Object.assign({}, this.options, options);
        this.render();
    }
}
