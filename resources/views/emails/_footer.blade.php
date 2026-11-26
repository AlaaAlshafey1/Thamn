{{--
Shared e-mail footer (dark bar): centred logo + address on top, sleek dark-outlined social icons in a centred row below.
Social links come from the dashboard (Contacts > social media) via Contact::emailSocialLinks().

Expected variables: $dir, $isRtl, $assetBase (public https base URL)
--}}
@php
  $canEmbed = isset($message) && is_object($message) && method_exists($message, 'embed');
  $footerLogoPath = public_path('assets/emails/logo_white.png');
  $footerLogo = ($canEmbed && is_file($footerLogoPath))
    ? $message->embed($footerLogoPath)
    : rtrim($assetBase, '/') . '/assets/emails/logo_white.png';
  $footerSocials = $socialLinks ?? \App\Models\Contact::emailSocialLinks($assetBase);
  $socialCount = count($footerSocials);
  $iconSize = 30;
  $iconRadius = 8;
  $socialRows = $socialCount > 0 ? array_chunk($footerSocials, 6) : [];
@endphp
<tr>
  <td class="footer-cell" style="background:#161820;padding:24px 24px 22px;border-radius:0 0 20px 20px;text-align:center;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="direction:{{ $dir }};">
      {{-- Centred logo --}}
      <tr>
        <td align="center" style="text-align:center;">
          <img src="{{ $footerLogo }}" width="54" height="52" alt="ثمّن"
            style="display:block;border:0;margin:0 auto 8px;">
        </td>
      </tr>

      {{-- Centred address --}}
      <tr>
        <td align="center" style="text-align:center;">
          <p style="margin:0;font-size:12px;line-height:1.6;color:#9A9A9A;font-family:'IBM Plex Sans Arabic',Tahoma,Arial,sans-serif;text-align:center;">
            {{ $isRtl ? 'المملكة العربية السعودية' : 'Kingdom of Saudi Arabia' }}
          </p>
        </td>
      </tr>

      {{-- Subtle divider + centred compact social icons --}}
      @if($socialCount > 0)
        <tr>
          <td align="center" style="padding:14px 0 14px;">
            <table width="140" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto;">
              <tr>
                <td style="border-top:1px solid #2A2D38;font-size:0;line-height:0;height:1px;">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td align="center">
            <table cellpadding="0" cellspacing="0" border="0" align="center" style="direction:ltr;margin:0 auto;">
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
                          <td style="padding:0 5px;">
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
        </tr>
      @endif
    </table>
  </td>
</tr>
