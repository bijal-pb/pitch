Hey, <strong>{{$user->first_name}}</strong>.
<br /><br />
<p> Sending invitation to join the {{ $business->startup_name }} @if($business->position != null) as position {{ $business->position }}. @else . @endif For join click on accept.</p>
<br />
<a href="{{ $link }}"> Accept </a>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<p> Thanks & Regards,</p>
<p> Pitch Team </p>
<p> <b>Email:</b> admin@pitch.com </p>


