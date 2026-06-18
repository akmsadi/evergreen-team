<!doctype html>
<html lang="en">

    <head>
        <title>Contact Form 05</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700' rel='stylesheet' type='text/css' media="print" onload="this.media='all';this.onload=null;">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" fetchpriority="high">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            .ftco-section {
                padding: 7em 0;
            }

            .form-control {
                height: 40px;
                background: transparent;
                color: rgba(0, 0, 0, 0.8) !important;
                font-size: 14px;
                border-radius: 0px;
                -webkit-box-shadow: none !important;
                box-shadow: none !important;
                border: 1px solid rgba(0, 0, 0, 0.1);
            }

            .form-control::-webkit-input-placeholder {
                /* Chrome/Opera/Safari */
                color: rgba(0, 0, 0, 0.5) !important;
            }

            .form-control::-moz-placeholder {
                /* Firefox 19+ */
                color: rgba(0, 0, 0, 0.5) !important;
            }

            .form-control:-ms-input-placeholder {
                /* IE 0+ */
                color: rgba(0, 0, 0, 0.5) !important;
            }

            .form-control:-moz-placeholder {
                /* Firefox 18- */
                color: rgba(0, 0, 0, 0.5) !important;
            }

            .form-control:focus,
            .form-control:active {
                background: transparent;
                border-color: rgba(0, 0, 0, 0.3) !important;
            }

            .wrapper {
                width: 100%;
                overflow: hidden;
                border-radius: 10px;
                -webkit-box-shadow: 0px 21px 41px -13px rgba(0, 0, 0, 0.18);
                -moz-box-shadow: 0px 21px 41px -13px rgba(0, 0, 0, 0.18);
                box-shadow: 0px 21px 41px -13px rgba(0, 0, 0, 0.18);
            }

            .contact-wrap {
                background: #fff;
            }

            .contact-wrap h3 {
                color: #000;
            }

            .info-wrap {
                background-size: 80%;
                background-repeat: no-repeat;
                background-position: center center;
            }

            @media (max-width: 991.98px) {
                .info-wrap {
                    height: 400px;
                }
            }

            .dbox {
                width: 100%;
                margin-bottom: 25px;
            }

            @media (min-width: 768px) {
                .dbox {
                    margin-bottom: 0;
                }
            }

            .dbox p {
                margin-bottom: 0;
            }

            .dbox p span {
                font-weight: 400;
                color: rgba(0, 0, 0, 0.2);
                display: block;
                text-transform: uppercase;
                font-size: 13px;
                font-weight: 600;
            }

            .dbox p a {
                color: rgba(0, 0, 0, 0.7);
            }

            .dbox .text {
                width: 100%;
            }

            .contactForm .form-control {
                border: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                padding: 0;
            }

            #contactForm .error {
                color: red;
                font-size: 12px;
            }

            #contactForm .form-control {
                font-size: 16px;
            }
        </style>

    </head>

    <body>
        <section class="ftco-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="wrapper">
                            <div class="row no-gutters">
                                <div class="col-lg-6 d-flex align-items-stretch">
                                    <div class="info-wrap w-100 p-5 img" style="background-image: url(assets/images/logo/challengers-logo.png);">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="contact-wrap w-100 p-md-5 p-4">
                                        <h3>Contact us</h3>
                                        <p class="mb-4">We're open for any suggestion or just to have a chat</p>
                                        <div id="form-message-warning" class="mb-4"></div>
                                        <div id="form-message-success" class="mb-4">
                                            Your message was sent, thank you!
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="dbox w-100 d-flex align-items-start">
                                                    <div class="text">
                                                        <p><span>Address:</span> 198 West 21th Street, Suite 721 New York NY 10016</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="dbox w-100 d-flex align-items-start">
                                                    <div class="text">
                                                        <p><span>Email:</span> <a href="mailto:info@yoursite.com">info@yoursite.com</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="dbox w-100 d-flex align-items-start">
                                                    <div class="text">
                                                        <p><span>Phone:</span> <a href="tel://1234567920">+ 1235 2355 98</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form method="POST" id="contactForm" name="contactForm" class="contactForm">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <textarea name="message" class="form-control" id="message" cols="30" rows="4" placeholder="Create a message here"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="submit" value="Send Message" class="btn btn-primary">
                                                        <div class="submitting"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>

</html>