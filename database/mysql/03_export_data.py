"""
Step 3: Export data from SQLite and generate MySQL INSERT statements.
Run: python3 03_export_data.py
Output: 03_data.sql
"""

import sqlite3, os, re
from datetime import datetime

sqlite_path = os.path.join(os.path.dirname(__file__), '..', 'newsora.sqlite')
out_path    = os.path.join(os.path.dirname(__file__), '03_data.sql')

def quote(val):
    if val is None:
        return 'NULL'
    s = str(val)
    s = s.replace('\\', '\\\\')
    s = s.replace("'", "\\'")
    s = s.replace('\r\n', '\\n')
    s = s.replace('\n', '\\n')
    return "'" + s + "'"

conn = sqlite3.connect(sqlite_path)
conn.row_factory = sqlite3.Row

tables = ['users', 'articles', 'bookmarks', 'comments', 'preferences', 'recovery_requests']

lines = [
    '-- Auto-generated data export from newsora.sqlite',
    '-- Run: mysql -u newsora_user -p newsora < 03_data.sql',
    '',
    'USE newsora;',
    'SET FOREIGN_KEY_CHECKS = 0;',
    '',
]

for table in tables:
    rows = conn.execute(f'SELECT * FROM {table}').fetchall()
    if not rows:
        continue
    cols = rows[0].keys()
    lines.append(f'-- Table: {table} ({len(rows)} rows)')
    for row in rows:
        vals = ', '.join(quote(row[c]) for c in cols)
        col_list = ', '.join(f'`{c}`' for c in cols)
        lines.append(f'INSERT INTO `{table}` ({col_list}) VALUES ({vals});')
    lines.append('')

lines.append('SET FOREIGN_KEY_CHECKS = 1;')
lines.append('')

with open(out_path, 'w', encoding='utf-8') as f:
    f.write('\n'.join(lines))

print(f"Exported to {out_path}")
conn.close()

