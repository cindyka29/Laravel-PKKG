<table class="table-condensed" style=" width:100%; padding-right: 40px;font-size:11px;">
    <thead>
    <tr class="col-no-border">
        <td colspan="10"><h3>Laporan Kas</h3></td>
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
        <th style="width:10%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Tujuan</th>
        <th style="width:8%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Tanggal</th>
        <th style="width:8%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Tipe</th>
        <th style="width:12%;text-align: left;" class="no-padding pl-5 pr-5 pt-10 pb-10">Keterangan</th>
        <th style="width:10%;text-align: right;" class="no-padding pl-5 pr-15 pt-10 pb-10 text-right">Nominal</th>
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
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$val->tujuan}}</td>
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{Carbon\Carbon::createFromFormat("Y-m-d",$val->date)->format("d F Y")}}</td>
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$val->type === "in" ? "Pemasukan" : "Pengeluaran"}}</td>
            <td style="width:8%;"  class="no-padding pl-5 pr-5 pt-10 pb-10">{{$val->pengeluaran}}</td>
            <td style="width:10%;text-align: right;" class="no-padding pl-5 pr-15 pt-10 pb-10 text-right">{{$val->type === "in" ? number_format($val->nominal,2,",",".") : "(".number_format($val->nominal,2,",",".").")"}}</td>
        </tr>
        @php $val->type === "in" ? $nominal += $val->nominal : $nominal -= $val->nominal; @endphp
    @endforeach

    </tbody>
    <tfoot style="background-color:#fafafa;border-top: 1px solid #ddd;">
    <tr>
        <td colspan="6" style="width:90%;padding-bottom: 20px;"  class="no-padding pl-5 pr-5 pt-10 pb-10 text-right text-medium"><b>TOTAL</b></td>
        <td style="width:10%;padding-bottom: 10px;text-align: right;" class="no-padding pl-5 pr-15 pt-10 pb-10 text-right text-medium"><b>{{$nominal < 0 ? "(".number_format($nominal,2,",",".").")" : number_format($nominal,2,",",".")}}</b></td>
    </tr>
    </tfoot>
</table>
