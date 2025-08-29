<!DOCTYPE html>
<html lang="ar">
@inject('Carbon', 'Carbon\Carbon')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: system-ui;
        }

        body {
            background-color: #FFFDFD;
            direction: {{ trans('config.dir') }};
            padding: 20px;

        }

        ::selection {
            background-color: #FA7070;
            color: #fff;
        }

        a {
            color: #FA7070;
        }

        p {
            color: #1B1C52;
        }

        .box {
            max-width: 400px;
            background-color: #F9F9F9;
            overflow: hidden;
            border-radius: 25px;
            margin: 50px auto;
            position: relative;
            /* padding-right: 150px; */
            /* margin-top: 100px; */

        }

        .box .patt {
            position: absolute;
            right: 8px;
            top: -39px;
        }

        .box .footer {
            @if (trans('config.dir') == 'rtl')
                margin: 0 auto 0 10px;
            @else
                margin: 0 10px 0 auto;
            @endif

            width: fit-content;
            /* position: absolute;
            left: 37px;

            bottom: 20px; */

        }

        .box .footer .logo {
            width: 90px;
        }

        .box .header {
            background-image: url("{{ env('FRONTEND_URL') }}/images/background.png");
            /* background-image: url("https://testappsa.netlify.app/background.png"); */
            width: 100%;
            height: 120px;
            background-size: 157px;
            border-radius: 25px;

        }

        .box .content {
            padding: 20px;
        }

        .box .container {
            padding-bottom: 80px;

        }


        .box .container .title {
            color: #FA7070;
            margin-top: 10px;
            word-break: break-word;
        }

        .main-btn {
            background-color: #FA7070;
            padding: 7px 16px;
            border-radius: 15px;
            -webkit-text-decoration: none;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            font-size: 16px;
            border: none;
            cursor: pointer;
            display: block;
            margin-top: 15px;
            width: fit-content;
        }

        .main-btn-lite {
            background-color: #fa70704d;
            ;
            padding: 7px 16px;
            border-radius: 15px;
            -webkit-text-decoration: none;
            text-decoration: none;
            color: #FA7070;
            font-weight: 500;
            font-size: 16px;
            border: none;
            cursor: pointer;
            width: fit-content;
            display: block;
            margin-top: 15px;
        }

        .center {
            margin: 0 auto;
        }

        .code {
            text-align: center;
            padding: 10px;
            background-color: #fa70704d;
            color: #FA7070;
            border-radius: 15px;
            font-size: 30px;
            margin-top: 10px;
        }

        .course {
            margin-top: 15px;
            border-radius: 12px;
            border: 1px dashed #FA7070;
            padding: 10px;

        }

        .course h3 {
            color: #FA7070;
            word-break: break-word;
        }

        .course ul {
            list-style-position: inside;
            color: #1B1C52;

        }

        .juzr {

            padding: 10px;
            background-color: #000;
            text-align: center;

        }

        .juzr p {
            color: #fff;
        }

        /* .juzr p {

        } */

        .juzr .logo {
            width: 50px;
            margin-top: 5px;
        }

        .juzr .off-notifi {
            font-size: 14px;
        }

        .juzr .copy {
            margin-top: 20px;
        }

        .juzr .copy .social a {
            display: inline-block;
            margin: 10px 5px;


        }
    </style>
    {{-- <style>
        @media (max-width:610px) {
            .box {
                width: 90%;

            }
        }
    </style> --}}
</head>

<body>
    <div class="box">
        <div class="content">
            <div class="header">

            </div>

            <div class="container">
                <h1 class="title">{{ trans('email.summary.text.title') }} {{ $user->name }}</h1>
                <p>{{ trans('email.summary.text.today') }}</p>

                @foreach ($courses as $course)
                    <div class="course">
                        <h3>{{ $course->name }}</h3>

                        <ul>
                            @foreach ($course->lessons as $lesson)
                                <li>{{ $lesson->name }}
                                    @if (
                                        $Carbon
                                            ::parse($lesson->exp_date_done, $user->timezone)->startOfDay()->lessThan(now($user->timezone)->startOfDay()))
                                        ({{ trans('email.summary.late') }})
                                    @endif
                                </li>

                                @if ($loop->index >= 9)
                                    @if ($loop->count - 10 > 0)
                                        <p>{{ trans('email.summary.plus') }} {{ $loop->count - 10 }}+</p>
                                    @endif
                                @break
                            @endif
                        @endforeach
                    </ul>



                </div>
            @endforeach
            <br />
            <a href="{{ env('FRONTEND_URL') }}/dashboard/courses"
                class="main-btn center">{{ trans('email.summary.btn') }}</a>
        </div>


        <div class="footer">
            <a href="{{ env('FRONTEND_URL') }}" target="_blank"><img alt="logo" width="auto" height="auto" class="logo" alt="image"
                    src="{{ env('FRONTEND_URL') }}/images/full-logo.png"></a>
            {{-- <a href="{{ env('FRONTEND_URL') }}" target="_blank"><img class="logo" alt="image"
                    src="https://testappsa.netlify.app/full-logo.png"></a> --}}
        </div>
    </div>


    <div class="juzr">
        <p class="off-notifi">
            {{ trans('email.footer.off-notifi.0') }} <a
                href="{{ env('JUZR_FRONTEND_URL') }}/account/notifications" target="_blank">
                {{ trans('email.footer.off-notifi.1') }}</a>
        </p>
        <div class="copy">
            <p>{{ trans('email.footer.OneOfPro') }} {{ trans('name.juzr') }}</p>

            <a href="{{ env('JUZR_FRONTEND_URL') }}" target="_blank"><img alt="logo" width="auto" height="auto" class="logo"
                    src="{{ env('JUZR_FRONTEND_URL') }}/images/juzr-white.png"></a>
            {{-- <a href="{{ env('JUZR_FRONTEND_URL') }}" target="_blank"><img class="logo"
                    src="https://testappsa.netlify.app/juzr-white.png"></a> --}}
            <div class="social">
                <a target="_blank" href="https://x.com/juzr_official"><img alt="x" width="auto" height="auto"
                        src="{{ env('JUZR_FRONTEND_URL') }}/images/social/twitter-x-line.png"></a>
                <a target="_blank" href="https://www.instagram.com/juzr.official/"><img alt="instagram" width="auto" height="auto"
                        src="{{ env('JUZR_FRONTEND_URL') }}/images/social/instagram-line.png"></a>
            </div>

            {{-- <div class="social">
                <a target="_blank" href="https://x.com/juzr_official"><img
                        src="https://testappsa.netlify.app/twitter-x-line.png"></a>
                <a target="_blank" href="https://www.instagram.com/juzr.official/"><img
                        src="https://testappsa.netlify.app/instagram-line.png"></a>
            </div> --}}

            </div>

        </div>
    </div>
</body>

</html>
