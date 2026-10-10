from __future__ import annotations

import html
import shutil
import subprocess
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "public" / "announcements" / "whatsapp-promo"
FRAMES = OUT / "frames"
SIZE = (720, 1280)
WHATSAPP_LINK = "https://wa.me/2349163128718?text=Hello%20OresamSub%2C%20I%20want%20to%20buy%20data%20or%20airtime"
FONT = "/System/Library/Fonts/Supplemental/Arial.ttf"


def wrap(text: str, limit: int = 31) -> list[str]:
    words = text.split()
    lines: list[str] = []
    current: list[str] = []

    for word in words:
        candidate = " ".join([*current, word])
        if len(candidate) > limit and current:
            lines.append(" ".join(current))
            current = [word]
        else:
            current.append(word)

    if current:
        lines.append(" ".join(current))

    return lines


def text_lines(lines: list[str], x: int, y: int, size: int, fill: str = "#0f172a", weight: int = 700, anchor: str = "start", gap: float = 1.25) -> str:
    return "\n".join(
        f'<text x="{x}" y="{y + int(i * size * gap)}" font-family="Arial, Helvetica, sans-serif" '
        f'font-size="{size}" font-weight="{weight}" text-anchor="{anchor}" fill="{fill}">{html.escape(line)}</text>'
        for i, line in enumerate(lines)
    )


def bubble(text: str, y: int, side: str = "left", color: str = "#ffffff", text_color: str = "#111827", width: int = 455) -> str:
    lines = wrap(text, 29)
    height = max(70, 34 + len(lines) * 32)
    x = 52 if side == "left" else 720 - 52 - width
    radius = 26
    anchor_x = x + 24
    return f"""
    <rect x="{x}" y="{y}" width="{width}" height="{height}" rx="{radius}" fill="{color}" filter="url(#softShadow)" />
    {text_lines(lines, anchor_x, y + 42, 25, text_color, 700)}
    """


def quick_reply(label: str, x: int, y: int, width: int = 170) -> str:
    return f"""
    <rect x="{x}" y="{y}" width="{width}" height="54" rx="27" fill="#dcfce7" stroke="#25D366" stroke-width="2"/>
    <text x="{x + width / 2}" y="{y + 35}" font-family="Arial, Helvetica, sans-serif" font-size="22" font-weight="900" text-anchor="middle" fill="#047857">{html.escape(label)}</text>
    """


def phone_frame(title: str, subtitle: str, messages: list[dict], step: str, footer: str, mode: str) -> str:
    message_markup = []
    for item in messages:
        if item.get("type") == "reply":
            message_markup.append(quick_reply(item["text"], item["x"], item["y"], item.get("width", 170)))
        else:
            message_markup.append(
                bubble(
                    item["text"],
                    item["y"],
                    item.get("side", "left"),
                    item.get("color", "#ffffff"),
                    item.get("text_color", "#111827"),
                    item.get("width", 455),
                )
            )

    return f"""<svg xmlns="http://www.w3.org/2000/svg" width="720" height="1280" viewBox="0 0 720 1280">
      <defs>
        <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stop-color="#022c22"/>
          <stop offset="50%" stop-color="#047857"/>
          <stop offset="100%" stop-color="#25D366"/>
        </linearGradient>
        <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
          <feDropShadow dx="0" dy="8" stdDeviation="9" flood-color="#064e3b" flood-opacity="0.23"/>
        </filter>
      </defs>

      <rect width="720" height="1280" fill="url(#bg)"/>
      <circle cx="640" cy="110" r="190" fill="rgba(255,255,255,0.10)"/>
      <circle cx="70" cy="1120" r="220" fill="rgba(255,255,255,0.10)"/>

      <text x="360" y="70" font-family="Arial, Helvetica, sans-serif" font-size="23" font-weight="900" text-anchor="middle" fill="#bbf7d0" letter-spacing="4">{html.escape(mode)}</text>
      <text x="360" y="124" font-family="Arial, Helvetica, sans-serif" font-size="46" font-weight="950" text-anchor="middle" fill="#ffffff">{html.escape(title)}</text>
      <text x="360" y="164" font-family="Arial, Helvetica, sans-serif" font-size="24" font-weight="700" text-anchor="middle" fill="#dcfce7">{html.escape(subtitle)}</text>

      <g filter="url(#softShadow)">
        <rect x="75" y="205" width="570" height="860" rx="50" fill="#f0fdf4"/>
        <rect x="75" y="205" width="570" height="92" rx="50" fill="#075e54"/>
        <rect x="75" y="250" width="570" height="47" fill="#075e54"/>
        <circle cx="132" cy="252" r="30" fill="#25D366"/>
        <text x="132" y="263" font-family="Arial, Helvetica, sans-serif" font-size="27" font-weight="900" text-anchor="middle" fill="#ffffff">O</text>
        <text x="178" y="247" font-family="Arial, Helvetica, sans-serif" font-size="27" font-weight="900" fill="#ffffff">OresamSub</text>
        <text x="178" y="276" font-family="Arial, Helvetica, sans-serif" font-size="17" font-weight="700" fill="#d1fae5">online • WhatsApp purchase</text>
        <rect x="506" y="232" width="84" height="38" rx="19" fill="rgba(255,255,255,0.16)"/>
        <text x="548" y="257" font-family="Arial, Helvetica, sans-serif" font-size="16" font-weight="900" text-anchor="middle" fill="#ffffff">{html.escape(step)}</text>

        <rect x="102" y="322" width="516" height="628" rx="30" fill="#e7f6ea"/>
        {"".join(message_markup)}

        <rect x="102" y="975" width="516" height="62" rx="31" fill="#ffffff"/>
        <text x="132" y="1014" font-family="Arial, Helvetica, sans-serif" font-size="22" font-weight="700" fill="#94a3b8">Message OresamSub...</text>
        <circle cx="585" cy="1006" r="25" fill="#25D366"/>
        <path d="M575 1006 L591 996 L586 1016 Z" fill="#ffffff"/>
      </g>

      <rect x="110" y="1102" width="500" height="76" rx="38" fill="#ffffff"/>
      <text x="360" y="1151" font-family="Arial, Helvetica, sans-serif" font-size="29" font-weight="950" text-anchor="middle" fill="#047857">Tap announcement button to open WhatsApp</text>
      <text x="360" y="1222" font-family="Arial, Helvetica, sans-serif" font-size="27" font-weight="900" text-anchor="middle" fill="#ffffff">{html.escape(footer)}</text>
    </svg>"""


def build_gif(name: str, frames: list[dict], delay: int) -> Path:
    FRAMES.mkdir(parents=True, exist_ok=True)
    pngs: list[Path] = []

    for index, frame in enumerate(frames, start=1):
        svg_path = FRAMES / f"{name}-{index:02d}.svg"
        png_path = FRAMES / f"{name}-{index:02d}.png"
        svg_path.write_text(phone_frame(**frame))
        subprocess.run(["magick", "-font", FONT, "-background", "none", str(svg_path), str(png_path)], check=True)
        pngs.append(png_path)

    gif_path = OUT / f"{name}.gif"
    subprocess.run(["magick", "-delay", str(delay), "-loop", "0", *map(str, pngs), "-layers", "Optimize", str(gif_path)], check=True)
    return gif_path


def main() -> None:
    if not shutil.which("magick"):
        raise SystemExit("ImageMagick 'magick' command is required.")

    OUT.mkdir(parents=True, exist_ok=True)

    power_messages = [
        [
            {"text": "Hi! Reply DATA or AIRTIME to buy instantly.", "y": 350},
            {"text": "DATA", "side": "right", "color": "#dcf8c6", "y": 455, "width": 210},
        ],
        [
            {"text": "Choose network: MTN, Airtel, Glo or 9mobile.", "y": 350},
            {"type": "reply", "text": "MTN", "x": 132, "y": 462},
            {"type": "reply", "text": "Airtel", "x": 322, "y": 462},
            {"text": "MTN", "side": "right", "color": "#dcf8c6", "y": 540, "width": 190},
        ],
        [
            {"text": "Select data plan: 1GB weekly, 2GB monthly, 5GB monthly...", "y": 350},
            {"text": "1GB weekly", "side": "right", "color": "#dcf8c6", "y": 485, "width": 280},
        ],
        [
            {"text": "Enter recipient phone number.", "y": 350},
            {"text": "08012345678", "side": "right", "color": "#dcf8c6", "y": 455, "width": 320},
            {"text": "Confirm 1GB MTN weekly to 08012345678?", "y": 555},
        ],
        [
            {"text": "YES", "side": "right", "color": "#dcf8c6", "y": 350, "width": 170},
            {"text": "Successful! Your data purchase has been processed.", "y": 448},
        ],
    ]

    power_frames = [
        {"title": "BUY DATA ON WHATSAPP", "subtitle": "A real OresamSub chat flow", "messages": messages, "step": f"{i}/5", "footer": "Easy • Fast • Affordable", "mode": "POWER MODE"}
        for i, messages in enumerate(power_messages, start=1)
    ]

    step_messages = [
        [{"text": "Step 1: Open WhatsApp from the announcement button.", "y": 350}, {"text": "Hello", "side": "right", "color": "#dcf8c6", "y": 460, "width": 190}],
        [{"text": "Step 2: Type DATA or AIRTIME.", "y": 350}, {"text": "DATA", "side": "right", "color": "#dcf8c6", "y": 455, "width": 190}],
        [{"text": "Step 3: Pick your network and plan.", "y": 350}, {"type": "reply", "text": "MTN", "x": 132, "y": 462}, {"type": "reply", "text": "Glo", "x": 322, "y": 462}, {"text": "1GB weekly", "side": "right", "color": "#dcf8c6", "y": 540, "width": 280}],
        [{"text": "Step 4: Enter phone number and confirm.", "y": 350}, {"text": "08012345678", "side": "right", "color": "#dcf8c6", "y": 455, "width": 320}, {"text": "YES", "side": "right", "color": "#dcf8c6", "y": 548, "width": 170}],
        [{"text": "Done! Your transaction is processed quickly.", "y": 350}, {"text": "Receipt sent. Thank you for using OresamSub.", "y": 460}],
    ]

    step_frames = [
        {"title": "STEP-BY-STEP GUIDE", "subtitle": "How customers buy via WhatsApp", "messages": messages, "step": f"{i}/5", "footer": "OresamSub — Simple WhatsApp buying", "mode": "GUIDE MODE"}
        for i, messages in enumerate(step_messages, start=1)
    ]

    power = build_gif("oresamsub-whatsapp-real-flow-power", power_frames, 130)
    step = build_gif("oresamsub-whatsapp-real-flow-step-by-step", step_frames, 155)

    announcement_html = f"""
<p>🎉 <strong>NOW BUY DATA &amp; AIRTIME ON ORESAMSUB VIA WHATSAPP!</strong> 📲💚</p>
<p>You can now buy data and airtime directly through WhatsApp. No stress, no long process.</p>
<p>✅ <strong>Easy</strong> — Buy right from WhatsApp.<br>
⚡ <strong>Fast</strong> — Get your transactions done in no time.<br>
💰 <strong>Cheap</strong> — Enjoy affordable data and airtime prices.</p>
<p><a href="{WHATSAPP_LINK}" target="_blank" style="display:inline-block;background:#25D366;color:#ffffff;padding:12px 18px;border-radius:999px;font-weight:800;text-decoration:none;">📲 Open OresamSub on WhatsApp</a></p>
<p>💚 <strong>OresamSub — Easy, Fast &amp; Affordable!</strong></p>
"""
    (OUT / "announcement-html-with-whatsapp-button.html").write_text(announcement_html.strip())
    (OUT / "whatsapp-link.txt").write_text(WHATSAPP_LINK + "\n")

    print(power)
    print(step)
    print(OUT / "announcement-html-with-whatsapp-button.html")
    print(OUT / "whatsapp-link.txt")


if __name__ == "__main__":
    main()
