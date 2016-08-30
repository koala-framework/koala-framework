<html>
<head>
    <title>503 <?=trlKwf('Service Unavailable')?></title>
    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        @-moz-keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        @-webkit-keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadein {
            0% {
                opacity: 0;
            }
            40% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        @-moz-keyframes fadein {
            0% {
                opacity: 0;
            }
            40% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        @-webkit-keyframes fadein {
            0% {
                opacity: 0;
            }
            40% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes fadeinHeadline {
            0% {
                transform: translate3d(0, 78px, 0);
            }
            20% {
                transform: translate3d(0, 78px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }
        @-moz-keyframes fadeinHeadline {
            0% {
                transform: translate3d(0, 78px, 0);
            }
            20% {
                transform: translate3d(0, 78px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }
        @-webkit-keyframes fadeinHeadline {
            0% {
                transform: translate3d(0, 78px, 0);
            }
            20% {
                transform: translate3d(0, 78px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes fadeinLine {
            0% {
                width: 0px;
            }
            50% {
                width: 0px;
            }
            100% {
                width: 570px;
            }
        }
        @-moz-keyframes fadeinLine {
            0% {
                width: 0px;
            }
            50% {
                width: 0px;
            }
            100% {
                width: 570px;
            }
        }
        @-webkit-keyframes fadeinLine {
            0% {
                width: 0px;
            }
            50% {
                width: 0px;
            }
            100% {
                width: 570px;
            }
        }

        @keyframes fadeinText {
            0% {
                transform: translate3d(0, -50px, 0);
            }
            50% {
                transform: translate3d(0, -50px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }
        @-moz-keyframes fadeinText {
            0% {
                transform: translate3d(0, -50px, 0);
            }
            50% {
                transform: translate3d(0, -50px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }
        @-webkit-keyframes fadeinText {
            0% {
                transform: translate3d(0, -50px, 0);
            }
            50% {
                transform: translate3d(0, -50px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }
        @keyframes fadeinButton {
            0% {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            60% {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        @-moz-keyframes fadeinButton {
            0% {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            60% {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        @-webkit-keyframes fadeinButton {
            0% {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            60% {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            100% {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        body {
            margin: 0;
        }

        .item {
            position: relative;
            top: 36vh;
        }

        @media (max-width: 710px) {
            .item {
                top: 10%
            }
        }

        .outerReload {
            overflow: hidden;
            height: 60px;
        }

        .reload {
            font-family: Helvetica, Arial;
            text-transform: uppercase;
            border: 2px solid black;
            text-decoration: none;
            letter-spacing: 2px;
            line-height: 22px;
            font-weight: bold;
            padding: 0 25px;
            font-size: 12px;
            display: table;
            margin: 0 auto;
            margin-top: 20px;
            color: black;
            height: 21px;
            animation: fadeinButton 1.464s cubic-bezier(0.77, 0, 0.175, 1);
            -moz-animation: fadeinButton 1.464s cubic-bezier(0.77, 0, 0.175, 1);
            -webkit-animation: fadeinButton 1.464s cubic-bezier(0.77, 0, 0.175, 1);
        }

        .reload:hover {
            opacity: 0.65;
        }

        .reload:active {
            opacity: 1;
        }

        .item2, .item3 {
            animation: fadein 3s cubic-bezier(0.77, 0, 0.175, 1);
            -moz-animation: fadein 3s cubic-bezier(0.77, 0, 0.175, 1);
            -webkit-animation: fadein 3s cubic-bezier(0.77, 0, 0.175, 1);
        }

        .item2 {
            background: url(data:image/png;base64,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) no-repeat center center transparent;
            margin-left: -285px;
            position: absolute;
            width: 285px;
            height: 60px;
            left: 50%;
            top: 72%;
        }

        @media (min-device-pixel-ratio: 1.1), (-webkit-min-device-pixel-ratio: 1.1) {
            .item2 {
                background: url(data:image/png;base64,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) no-repeat center center white;
                background-size: 285px 60px;
            }
        }

        @media (max-width: 710px) {
            .item2 {
                position: relative;
                margin: 0 auto;
                width: 90%;
                top: 25%;
                left: 0;
            }
        }

        .item3 {
            background: url(data:image/png;base64,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) no-repeat center center transparent;
            margin-right: -285px;
            position: absolute;
            width: 285px;
            height: 60px;
            right: 50%;
            top: 72%;
        }

        @media (min-device-pixel-ratio: 1.1), (-webkit-min-device-pixel-ratio: 1.1) {
            .item3 {
                background: url(data:image/png;base64,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) no-repeat center center white;
                background-size: 285px 60px;
            }
        }

        @media (max-width: 710px) {
            .item3 {
                position: relative;
                margin: 0 auto;
                width: 90%;
                top: 33%;
                right: 0;
            }
        }


        .item h1 {
            animation: fadeinHeadline 1s cubic-bezier(0.77, 0, 0.175, 1);
            -moz-animation: fadeinHeadline 1s cubic-bezier(0.77, 0, 0.175, 1);
            -webkit-animation: fadeinHeadline 1s cubic-bezier(0.77, 0, 0.175, 1);
            margin: 0 auto;
            width: 710px;
            height: 64px;
            background: url(data:image/png;base64,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) no-repeat center center transparent;
        }

        @media (min-device-pixel-ratio: 1.1), (-webkit-min-device-pixel-ratio: 1.1) {
            .item h1 {
                background: url(data:image/png;base64,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) no-repeat center center white;
                background-size: 355px 49px;
            }
        }

        @media (max-width: 710px) {
            .item h1 {
                background-size: 280px 38px;
                width: 90%;
            }
        }

        .item .outerHeadline {
            overflow: hidden;
            height: 78px;
        }

        .line {
            position: relative;
            background: black;
            margin: 0 auto;
            width: 570px;
            height: 1px;
        }

        @media (max-width: 710px) {
            .line {
                width: 90%;
            }
        }

        @media (min-width: 710px) {
            .item .line {
                animation: fadeinLine 1.24s cubic-bezier(0.77, 0, 0.175, 1);
                -moz-animation: fadeinLine 1.24s cubic-bezier(0.77, 0, 0.175, 1);
                -webkit-animation: fadeinLine 1.24s cubic-bezier(0.77, 0, 0.175, 1);
            }
        }

        .item .icon {
            background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAVCAYAAACpF6WWAAACaklEQVQ4jY3UXajNaRQG8N/ejhxfo5M0Ob5umNBMTVMyIzXiICGlXEhEksmFmZuZJlHTXBgXk2EkkfJ1IWrIhQsiFMKFjziTxuQjRIjGR8I5h4u1tv237V2zavd/3/Xu91lrPe+zVqm1tVUDG4pO3MVs/IhvMACDcaHRxXIDfwl/4GzuZ+HrXM/FSTT9X9CpCfYWf2MQtqEtz09jCZ6iAzPxWy1obbQe+AEj0St9C/Pbgc/RN/ebMQeX0B1v3peZnPZPkNv4ByNwFfuwBWswRdDwCQ4WEvkK1/C8NtPVeeFyBriaILfz/DjGCi4JzndgAfbjMUajmSqnS/OgDY+wrAAIvfPXreBbhHYMQwtWVQ7K+FTIZ3n69uCYD+11llcq+LowLtedWIc+lfK3Y4Iguws7fWyPM9POGv9TnBe83sMLDCsLYW8Ur3sTt+qAjkE/IbVaO1b4rkVLGS+xAq8EFcWLQ7BScNwNhzC8ThXwC9bjSRmb8ExI5W1mTDzEOXyfwDOyvHahluZCYLgi1KMJJ/BAvPw4welgfIE/8ZfoLjiMyUK7CwVtszNQewZtLhUGymJszfVRfIfr4vFqrQU/4efcH8lgUK7o9NcCINzHvw0A4Ql2q/LfptoYXZWO2is0uFn08ry8ML8OYJMYPJuExKaLbqpoVqlmnn6GM1lexXbioqo6vsV40Qz9RKvuymAd9UB/xyTB7xpVnmTmr8UkuyVmxXb8h4kKVNWCwkDRHXvwJaYJhfTPyweykl6Z2QDBcUWKdSf/vfzewAahgFO4k9SMEhOqZ/7vYRGwUaZFKw7fMaLPOwvJ1FXHO6wolnpLxWOyAAAAAElFTkSuQmCC) no-repeat center center white;
            margin-left: -14.5px;
            border-radius: 50%;
            position: absolute;
            left: 50%;
            height: 29px;
            width: 29px;
            z-index: 1;
            margin-top: -15px;
            animation: spin 3s infinite linear;
            -moz-animation: spin 3s infinite linear;
            -webkit-animation: spin 3s infinite linear;
        }

        @media (min-device-pixel-ratio: 1.1), (-webkit-min-device-pixel-ratio: 1.1) {
            .item .icon {
                background: url(data:image/png;base64,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) no-repeat center center white;
                background-size: 21px 21px;
            }
        }

        p {
            -webkit-font-smoothing: antialiased;
            text-align: center;
            font-weight: 200;
            margin-top: 16px;
            font-size: 20px;
            font-family: 'Helvetica Neue', 'Work Sans', Helvetica, Arial;
        }

        .item p {
            background: url(data:image/png;base64,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) no-repeat center center white;
            animation: fadeinText 1.24s cubic-bezier(0.77, 0, 0.175, 1);
            -moz-animation: fadeinText 1.24s cubic-bezier(0.77, 0, 0.175, 1);
            -webkit-animation: fadeinText 1.24s cubic-bezier(0.77, 0, 0.175, 1);
        }

        @media (min-device-pixel-ratio: 1.1), (-webkit-min-device-pixel-ratio: 1.1) {
            .item p {
                background: url(data:image/png;base64,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) no-repeat center center white;
                background-size: 329px 20px;
            }
        }

        .item .outerText {
            overflow: hidden;
            height: 50px;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
    <div class="item">
        <div class="outerHeadline">
            <h1></h1>
        </div>
        <div class="line"></div>
        <div class="icon"></div>
        <div class="outerText">
            <p>&nbsp;</p>
        </div>
        <div class="outerReload">
            <a class="reload" href=".">Try again</a>
        </div>
    </div>
    <div class="item2">
    </div>
    <div class="item3">
    </div>
</body></html>
