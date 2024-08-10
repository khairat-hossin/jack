<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <h1 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 text-center mtop25">
                    Report Generated For <?php echo $report_data->generate_dates; ?>
                    <br>
                    <a onclick="CreatePDFfromHTML()" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 generate-pdf">
                        <i class="fas fa-file-pdf fa-lg"></i>
                    </a>
                </h1>
                <div class="html-content">
                <?php
                echo $generated_report;
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
<script>
    function CreatePDFfromHTML() {
        var HTML_Width = $(".html-content").width();
        var HTML_Height = $(".html-content").height();
        var top_left_margin = 15;
        var PDF_Width = HTML_Width + (top_left_margin * 2);
        var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
        var canvas_image_width = HTML_Width;
        var canvas_image_height = HTML_Height;

        var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        html2canvas($(".html-content")[0]).then(function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg", 1.0);
            var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
            for (var i = 1; i <= totalPDFPages; i++) {
                pdf.addPage(PDF_Width, PDF_Height);
                pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
            }
            pdf.save("<?php echo 'ReportPlus - '. $report_data->generate_dates . ' Report';  ?>.pdf");
        });
    }

</script>
</html>