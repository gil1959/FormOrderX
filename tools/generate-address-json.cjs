/* tools/generate-address-json.js */
const fs = require("fs");
const path = require("path");
const geografis = require("geografis");

const outPath = path.join(
    __dirname,
    "..",
    "storage",
    "app",
    "address_suggest",
    "indonesia_kecamatan_labels.json"
);

function main() {
    const rows = geografis.dump();

    // dedup pakai key label (lowercase)
    const map = new Map();

    for (const r of rows) {
        if (!r) continue;

        const prov = (r.province || "").trim();
        const city = (r.city || "").trim();       // kab/kota
        const dist = (r.district || "").trim();   // kecamatan
        const vill = (r.village || "").trim();    // desa/kelurahan
        const postal = r.postal != null ? String(r.postal).trim() : "";

        if (!prov || !city || !dist || !vill) continue;

        const label = `${prov}, ${city}, ${dist}, ${vill}`;
        const key = label.toLowerCase();

        // kalau postal kosong pun simpan, tapi idealnya geografis ada postal
        if (!map.has(key)) {
            map.set(key, {
                label,
                value: label,
                postal: postal || null
            });
        } else {
            // kalau sudah ada tapi postal sebelumnya null dan sekarang ada, upgrade
            const existing = map.get(key);
            if ((!existing.postal || existing.postal === null) && postal) {
                existing.postal = postal;
                map.set(key, existing);
            }
        }
    }

    const list = Array.from(map.values()).sort((a, b) =>
        a.label.localeCompare(b.label, "id")
    );

    fs.mkdirSync(path.dirname(outPath), { recursive: true });
    fs.writeFileSync(outPath, JSON.stringify(list, null, 2), "utf8");

    console.log("OK:", list.length, "items =>", outPath);
}

main();
