<table class="table-condensed" style=" width:100%; padding-right: 40px;font-size:11px;">
    <thead>
    <tr class="col-no-border">
        <td colspan="10"><h3>Laporan Iuran</h3></td>
    </tr>
    @if(@isset($records))
        <tr class="col-no-border">
            <td colspan="2"><b>Bulan</b></td>
            <td colspan="7">: {{$date}}</td>
        </tr>
    @endif
    <tr>
        <td colspan="10" class="">&nbsp;</td>
    </tr>
    </thead>
    <thead style="background-color:#fafafa;border-top: 1px solid #ddd;">
    <tr>
        <th style="width:2%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">No.</th>
        <th style="width:18%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Kegiatan</th>
        <th style="width:8%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Tanggal</th>
        <th style="width:8%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Status</th>
        <th style="width:12%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @php
        $nominal = 0;
    @endphp
    @foreach ($records as $key => $val)
        <tr>
            <td style="width:20%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$key+1}}</td>
            <td style="width:20%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$val->activity->name}}</td>
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{Carbon\Carbon::createFromFormat("Y-m-d",$val->activity->date)->format("d F Y")}}</td>
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$val->is_paid === 1 ? "Sudah Bayar" : "Belum Bayar"}}</td>
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$val->activity->note}}</td>
        </tr>
    @endforeach

    </tbody>
</table>
