<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 4px 3px;
        }
        th {
            text-align: left;
        }
        .d-block {
            display: block;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .border-bottom-header {
            border-bottom: 1px solid;
        }
        .border-all, .border-all th, .border-all td {
            border: 1px solid;
        }
        .font-10 { font-size: 10pt; }
        .font-11 { font-size: 11pt; }
        .font-13 { font-size: 13pt; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img width="auto" height="80px" src="{{ asset('polinema-bw.png') }}"
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold">MINISTRY OF EDUCATION, CULTURE, RESEARCH AND TECHNOLOGY</span>
                <span class="text-center d-block font-13 font-bold">POLYTECHNIC NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Phone (0341) 404424 Pes. 101-105, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">USER DATA REPORT</h3>

    <table class="border-all">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Username</th>
                <th>Name</th>
                <th>Level</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->nama }}</td>
                <td>{{ $u->level->level_nama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
