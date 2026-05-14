$(document).ready(function() {
    $('.btn_down').on('click', function() {
        const { jsPDF } = window.jspdf;
        const element = document.querySelector('.print_page');
        const $this = $(this);

        $this.prop('disabled', true);

        const originalWidth = element.style.width;
        element.style.width = '794px'; 
        const noPrintElements = document.querySelectorAll('.no-print');
        noPrintElements.forEach(el => el.style.display = 'none');

        html2canvas(element, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: "#ffffff",
            width: 794,
            windowWidth: 794
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = 210;
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save('download_' + new Date().getTime() + '.pdf');

            element.style.width = originalWidth;
            noPrintElements.forEach(el => el.style.display = '');
            $this.prop('disabled', false);
        }).catch(err => {
            element.style.width = originalWidth;
            noPrintElements.forEach(el => el.style.display = '');
            $this.prop('disabled', false);
        });
    });

    $('.btn_print').on('click', function() {
        window.print();
    });
});