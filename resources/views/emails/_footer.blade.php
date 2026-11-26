{{--
Shared e-mail footer (dark bar): logo + address on one side, brand-coloured social icons on the other.
Social links come from the dashboard (Contacts > social media) via Contact::emailSocialLinks().

Expected variables: $dir, $isRtl, $assetBase (public https base URL)
--}}
@php
  $footerTxtAlign = $isRtl ? 'right' : 'left';
  $footerOppAlign = $isRtl ? 'left' : 'right';
  $canEmbed = isset($message) && is_object($message) && method_exists($message, 'embed');
  $footerLogoPath = public_path('assets/emails/logo_white.png');
  $footerLogo = ($canEmbed && is_file($footerLogoPath))
    ? $message->embed($footerLogoPath)
    : rtrim($assetBase, '/') . '/assets/emails/logo_white.png';
  $footerSocials = $socialLinks ?? \App\Models\Contact::emailSocialLinks($assetBase);
  $socialCount = count($footerSocials);

  if ($socialCount <= 3) {
    $perRow = 3;
    $iconSize = 38;
    $iconRadius = 10;
  } elseif ($socialCount === 4) {
    $perRow = 2;
    $iconSize = 34;
    $iconRadius = 9;
  } else {
    $perRow = 3;
    $iconSize = 34;
    $iconRadius = 9;
  }
  $socialRows = $socialCount > 0 ? array_chunk($footerSocials, $perRow) : [];
@endphp
<tr>
  <td class="footer-cell" style="background:#1A1A1A;padding:22px 28px;border-radius:0 0 20px 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
      <tr>
        {{-- Logo + address --}}
        <td align="{{ $footerTxtAlign }}" valign="middle" style="padding-{{ $isRtl ? 'left' : 'right' }}:14px;">
          <img src="{{ $footerLogo }}" width="64" height="62" alt="ثمّن"
            style="display:block;border:0;margin-bottom:8px;">
          <p style="margin:0;font-size:12px;line-height:1.6;color:#9A9A9A;font-family:'IBM Plex Sans Arabic',Tahoma,Arial,sans-serif;">
            {{ $isRtl ? 'المملكة العربية السعودية، الجبيل' : 'Saudi Arabia, Al Jubail' }}
          </p>
        </td>

        {{-- Social icons (from the dashboard, 1 row for <=3, clean grid for 4+) --}}
        @if($socialCount > 0)
          <td align="{{ $footerOppAlign }}" valign="middle">
            <table cellpadding="0" cellspacing="0" border="0" style="direction:ltr;">
              @foreach($socialRows as $row)
                <tr>
                  <td align="center" style="padding-bottom:{{ $loop->last ? '0' : '8px' }};">
                    <table cellpadding="0" cellspacing="0" border="0" align="center" style="direction:ltr;margin:0 auto;">
                      <tr>
                        @foreach($row as $social)
                          @php
                            $socialIconPath = $social['icon_path'] ?? public_path('assets/emails/social/' . ($social['platform'] ?? 'link') . '.png');
                            $socialIconSrc = ($canEmbed && is_file($socialIconPath))
                              ? $message->embed($socialIconPath)
                              : $social['icon'];
                          @endphp
                          <td style="padding:0 {{ $loop->last ? '0' : '4px' }} 0 {{ $loop->first ? '0' : '4px' }};">
                            <a href="{{ $social['url'] }}" target="_blank" title="{{ $social['name'] }}" style="text-decoration:none;">
                              <img src="{{ $socialIconSrc }}" width="{{ $iconSize }}" height="{{ $iconSize }}" alt="{{ $social['name'] }}"
                                style="display:block;border:0;width:{{ $iconSize }}px;height:{{ $iconSize }}px;border-radius:{{ $iconRadius }}px;">
                            </a>
                          </td>
                        @endforeach
                      </tr>
                    </table>
                  </td>
                </tr>
              @endforeach
            </table>
          </td>
        @endif
      </tr>
    </table>
  </td>
</tr>
