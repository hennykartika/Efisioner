<section class="info_section layout_padding2-top">
    <div class="social_container">
        <div class="social_box">
            <a href="">
                <img src="<?= base_url(); ?>/assets/images/fb.png" alt="">
            </a>
            <a href="">
                <img src="<?= base_url(); ?>/assets/images/twitter.png" alt="">
            </a>
            <a href="">
                <img src="<?= base_url(); ?>/assets/images/linkedin.png" alt="">
            </a>
            <a href="">
                <img src="<?= base_url(); ?>/assets/images/youtube.png" alt="">
            </a>
        </div>
    </div>
    <div class="info_container">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <h6>
                        <a href="http://" style="color: white;">Tentang Kami</a>
                    </h6>
                    <p>
                        Terima kasih telah mengunjungi website kami! Kami berkomitmen untuk memberikan pengalaman yang lebih baik dan berharga dalam website efisioner kami.
                    </p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h6>
                        <a href="http://" style="color: white;">Bantuan</a>
                    </h6>
                    <p>
                        Kami siap membantu! Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.
                    </p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h6>
                        <a href="http://" style="color: white;">Term & Condition</a>
                    </h6>
                    <p>
                        Mohon diperhatikan syarat dan ketentuan kami. Silakan membaca ketentuan yang berlaku sebelum menggunakan layanan kuesioner kami. </p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h6>
                        <a href="http://" style="color: white;">fAQ</a>
                    </h6>
                    <div class="info_link-box">
                        <a href="">
                            <img src="<?= base_url(); ?>/assets/images/location.png" alt="">
                            <span> Jalan Simpang Belitung </span>
                        </a>
                        <a href="">
                            <img src="<?= base_url(); ?>/assets/images/call.png" alt="">
                            <span>+6281254181775</span>
                        </a>
                        <a href="">
                            <img src="<?= base_url(); ?>/assets/images/mail.png" alt="">
                            <span> efisioner@gmail.com</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer section -->
    <section class=" footer_section">
        <div class="container">
            <!-- <p>
                &copy; <span id="displayYear"></span> All Rights Reserved By
                <a href="https://html.design/">Free Html Templates</a>
            </p> -->
        </div>
    </section>
    <!-- footer section -->

</section>

<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/bootstrap.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/custom.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

<script type="text/javascript">
    $(function() {
        let toast = $('.toast');
        toast.toast({
            delay: 3000,
            animation: true
        });
        toast.toast('show');
    })
</script>
</body>

</html>