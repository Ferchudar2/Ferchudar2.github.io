import os
import sqlite3
import tkinter as tk
import requests
import threading
from tkinter import filedialog, messagebox, simpledialog
from tkinter import ttk
import ttkbootstrap as tb
from ttkbootstrap.constants import *
from PIL import Image, ImageTk, ImageDraw, ImageFont
import pandas as pd
from datetime import datetime
from reportlab.lib.pagesizes import A4
from reportlab.pdfgen import canvas
import openpyxl
import shutil

# --- CONFIGURACIÓN DE RUTAS Y BASE DE DATOS ---
DB_FILE = 'ferreteria.db'
IMAGE_DIR = 'images'
THUMB_SIZE = (100, 100)
PREVIEW_SIZE = (300, 300)
os.makedirs(IMAGE_DIR, exist_ok=True)


# --- FUNCIONES AUXILIARES (PARSE Y PLACEHOLDER) ---
def parse_number_safe(val, default=0.0):
    try:
        if pd.isna(val):
            return default
        s = str(val).strip()
        s = s.replace('$', '').replace(' ', '')
        if s.count(',') and s.count('.') == 0:
            s = s.replace(',', '.')
        if s.count('.') and s.count(','):
            s = s.replace(',', '')
        s = ''.join(ch for ch in s if ch.isdigit() or ch == '.' or ch == '-')
        if s == '' or s == '.' or s == '-':
            return default
        return float(s)
    except Exception:
        return default


def parse_int_safe(val, default=0):
    try:
        f = parse_number_safe(val, default)
        return int(round(f))
    except Exception:
        return default

def obtener_dolares():
    """Obtiene el valor del dólar oficial y blue desde la API de Dólar Hoy"""
    try:
        resp = requests.get("https://dolarapi.com/v1/dolares", timeout=5)
        datos = resp.json()
        blue = next((d for d in datos if d["casa"] == "blue"), None)
        oficial = next((d for d in datos if d["casa"] == "oficial"), None)
        if blue and oficial:
            return float(oficial["venta"]), float(blue["venta"])
    except Exception as e:
        print("Error obteniendo dólares:", e)
    return None, None

def create_placeholder_image(name, filename, size=(300,300), color=(240,240,240)):
    """Crea una imagen placeholder con texto"""
    try:
        img = Image.new('RGB', size, color)
        draw = ImageDraw.Draw(img)
        try:
            fnt = ImageFont.truetype("arial.ttf", 20)
        except Exception:
            fnt = ImageFont.load_default()
        
        lines = []
        words = name.split()
        line = ""
        for w in words:
            if len(line + " " + w) > 15:
                lines.append(line.strip())
                line = w
            else:
                line += " " + w
        lines.append(line.strip())
        
        # Calcular altura total del texto
        total_text_height = len(lines) * 25
        start_y = (size[1] - total_text_height) // 2
        
        for i, line_text in enumerate(lines):
            bbox = draw.textbbox((0, 0), line_text, font=fnt)
            w = bbox[2] - bbox[0]
            draw.text(((size[0]-w)//2, start_y + i*25), line_text, fill=(100,100,100), font=fnt)
        
        img.save(filename, 'PNG')
        return True
    except Exception as e:
        print("Error creando placeholder:", e)
        return False


# --- CLASE DE GESTIÓN DE BASE DE DATOS ---
class FerreteriaDB:
    def __init__(self, path=DB_FILE,):
        self.conn = sqlite3.connect(path)
        self.create_tables()

    def create_tables(self):
        c = self.conn.cursor()
        
        # Tabla principal de productos
        c.execute('''
            CREATE TABLE IF NOT EXISTS productos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                codigo TEXT UNIQUE,
                descripcion TEXT,
                precio REAL,
                stock INTEGER,
                minimo INTEGER,
                categoria TEXT,
                imagen TEXT,
                importado INTEGER DEFAULT 0
            )
        ''')
        
        # Tabla de variantes
        c.execute('''
            CREATE TABLE IF NOT EXISTS variantes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                producto_id INTEGER,
                descripcion_detallada TEXT,
                precio REAL,
                stock INTEGER,
                codigo TEXT,
                FOREIGN KEY(producto_id) REFERENCES productos(id)
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS movimientos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                producto_id INTEGER,
                tipo TEXT,
                cantidad INTEGER,
                fecha TEXT
            )
        ''')
        self.conn.commit()
    
    def eliminar_producto(self, producto_id):
        """Elimina un producto y todas sus variantes asociadas de la base de datos."""
        c = self.conn.cursor()
        # Primero eliminar variantes asociadas
        c.execute("DELETE FROM variantes WHERE producto_id = ?", (producto_id,))
        # Luego eliminar el producto
        c.execute("DELETE FROM productos WHERE id = ?", (producto_id,))
        self.conn.commit()
    def eliminar_variante(self, variante_id):
        """Elimina una variante específica de la base de datos."""
        c = self.conn.cursor()
        c.execute("DELETE FROM variantes WHERE id = ?", (variante_id,))
        self.conn.commit()
    def actualizar_stock(self, producto_id, nuevo_stock):
        """Actualiza el stock de un producto según su ID."""
        c = self.conn.cursor()
        c.execute("UPDATE productos SET stock=? WHERE id=?", (nuevo_stock, producto_id))
        self.conn.commit()
    def registrar_movimiento(self, producto_id, tipo_movimiento, cantidad):
        """Registra un movimiento de stock (venta, compra, etc.)."""
        c = self.conn.cursor()
        c.execute(
            "INSERT INTO movimientos (producto_id, tipo, cantidad, fecha) VALUES (?, ?, ?, ?)",
            (producto_id, tipo_movimiento, cantidad, datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
        )
        self.conn.commit()
    def mostrar_imagen_producto(self, producto):
        # --- 1. VALIDACIÓN DE LA TUPLA ---
        # Verifica que 'producto' sea una tupla/lista y que tenga al menos 9 elementos.
        if not isinstance(producto, (list, tuple)) or len(producto) < 9:
            # Si no es un producto completo (ej. selección de cabecera o DB incompleta), limpia el preview.
            W_TARGET, H_TARGET = getattr(self, 'PREVIEW_SIZE', (300, 300))
            self.preview_label.configure(image='', text='Selecciona un producto válido.', width=W_TARGET, height=H_TARGET)
            self.preview_label.image = None
            return

        # --- 2. DESEMPAQUETADO SEGURO ---
        try:
            # Asumiendo que la imagen es el elemento 7 (índice 7)
            prod_id, codigo, descripcion, precio, stock, minimo, categoria, imagen_path, importado = producto
        except ValueError as e:
            print(f"Error al desempaquetar la tupla del producto (mostrar_imagen_producto): {e}")
            return

        # Intenta usar la constante PREVIEW_SIZE. Si no existe, usa 300x300.
        W_TARGET, H_TARGET = getattr(self, 'PREVIEW_SIZE', (300, 300))
        
        # 3. Determinar la ruta a cargar (imagen real o placeholder)
        if imagen_path and os.path.exists(imagen_path):
            ruta_a_cargar = imagen_path
        else:
            # Generar placeholder si no existe la imagen
            safe_name = "".join(c for c in descripcion.lower().replace(" ", "_") if c.isalnum() or c == '_')[:30]
            placeholder_path = os.path.join(IMAGE_DIR, f'{safe_name}_preview.png')
            # Asumo que IMAGE_DIR y create_placeholder_image están definidas globalmente
            if not os.path.exists(placeholder_path):
                create_placeholder_image(descripcion, placeholder_path, size=(W_TARGET, H_TARGET))
            ruta_a_cargar = placeholder_path

        # 4. Redimensionamiento y carga en Tkinter
        try:
            img = Image.open(ruta_a_cargar)
            
            # Cálculo de la proporción para que la imagen quepa completa
            w_original, h_original = img.size
            ratio_w = W_TARGET / w_original
            ratio_h = H_TARGET / h_original
            
            # Usamos el menor ratio para que quepa (solución a "se ve cortada")
            ratio = min(ratio_w, ratio_h)
            
            w_new = int(w_original * ratio)
            h_new = int(h_original * ratio)

            # Redimensionar la imagen
            img = img.resize((w_new, h_new), Image.Resampling.LANCZOS)
            
            # Manejo de transparencia (si es necesario)
            if img.mode in ('RGBA', 'LA'):
                background = Image.new('RGB', img.size, (255, 255, 255))
                background.paste(img, mask=img.split()[-1])
                img = background
            
            photo = ImageTk.PhotoImage(img)
            
            # 5. Mostrar y almacenar referencia
            self.preview_label.configure(image=photo, text='', width=w_new, height=h_new)
            self.preview_label.image = photo  # CRUCIAL: Mantener la referencia viva
            
        except Exception as e:
            print(f"Error fatal al mostrar imagen en preview: {e}")
            # Fallback si falla la carga o redimensionamiento
            self.preview_label.configure(image='', text='Error de Imagen', width=W_TARGET, height=H_TARGET)
            self.preview_label.image = None

    def agregar_producto(self, codigo, descripcion, precio, stock, minimo, categoria, imagen, importado=0):
        c = self.conn.cursor()
        try:
            c.execute('SELECT id FROM productos WHERE codigo=?', (codigo,))
            row = c.fetchone()
            if row:
                c.execute('''UPDATE productos SET descripcion=?, precio=?, stock=?, minimo=?, categoria=?, imagen=?, importado=?
                             WHERE codigo=?''', (descripcion, precio, stock, minimo, categoria, imagen, importado, codigo))
            else:
                c.execute('''INSERT INTO productos (codigo, descripcion, precio, stock, minimo, categoria, imagen, importado)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)''', (codigo, descripcion, precio, stock, minimo, categoria, imagen, importado))
            self.conn.commit()
            return True
        except Exception as e:
            print('DB error agregar_producto:', e)
            return False

    def obtener_producto_por_codigo(self, codigo):
        c = self.conn.cursor()
        c.execute('SELECT id, codigo, descripcion, precio, stock, minimo, categoria, imagen, COALESCE(importado,0) FROM productos WHERE codigo=?', (codigo,))
        return c.fetchone()

    def editar_producto(self, producto_id, codigo, descripcion, precio, stock, minimo, categoria, imagen, importado=0):
        c = self.conn.cursor()
        c.execute('''UPDATE productos SET codigo=?, descripcion=?, precio=?, stock=?, minimo=?, categoria=?, imagen=?, importado=?
                     WHERE id=?''', (codigo, descripcion, precio, stock, minimo, categoria, imagen, importado, producto_id))
        self.conn.commit()

    def eliminar_item(self, id, tipo):
        c = self.conn.cursor()
        if tipo == 'producto':
            c.execute('DELETE FROM variantes WHERE producto_id=?', (id,))
            c.execute('DELETE FROM productos WHERE id=?', (id,))
        elif tipo == 'variante':
            c.execute('DELETE FROM variantes WHERE id=?', (id,))
        self.conn.commit()


    def listar_productos(self):
        c = self.conn.cursor()
        c.execute('SELECT id, codigo, descripcion, precio, stock, minimo, categoria, imagen, COALESCE(importado,0) FROM productos ORDER BY descripcion')
        return c.fetchall()
    def obtener_producto_por_id(self, producto_id):
        """Devuelve todos los datos de un producto dado su ID."""
        c = self.conn.cursor()
        c.execute("""
            SELECT id, codigo, descripcion, precio, stock, minimo, categoria, imagen, importado
            FROM productos
            WHERE id = ?
        """, (producto_id,))
        return c.fetchone()


    # Variantes
    def agregar_variante(self, producto_id, descripcion_detallada, precio, stock, codigo):
        c = self.conn.cursor()
        try:
            c.execute('INSERT INTO variantes (producto_id, descripcion_detallada, precio, stock, codigo) VALUES (?,?,?,?,?)',
                      (producto_id, descripcion_detallada, float(precio), int(stock), codigo))
            self.conn.commit()
            return True
        except Exception as e:
            print('DB agregar_variante error:', e)
            return False

    def obtener_variante_por_id(self, variante_id):
        c = self.conn.cursor()
        c.execute('SELECT id, producto_id, descripcion_detallada, precio, stock, codigo FROM variantes WHERE id=?', (variante_id,))
        return c.fetchone()

    def editar_variante(self, variante_id, descripcion_detallada, precio, stock, codigo):
        c = self.conn.cursor()
        c.execute('UPDATE variantes SET descripcion_detallada=?, precio=?, stock=?, codigo=? WHERE id=?',
                  (descripcion_detallada, float(precio), int(stock), codigo, variante_id))
        self.conn.commit()

    def listar_variantes_por_producto(self, producto_id):
        c = self.conn.cursor()
        try:
            c.execute('SELECT id, producto_id, descripcion_detallada, precio, stock, codigo FROM variantes WHERE producto_id=? ORDER BY codigo',
                      (producto_id,))
            return c.fetchall()
        except sqlite3.OperationalError as e:
            print(f"Error al listar variantes: {e}")
            return []

    def obtener_producto_con_variantes(self):
        """Obtiene todos los productos con sus variantes"""
        productos = self.listar_productos()
        resultado = []
        for producto in productos:
            variantes = self.listar_variantes_por_producto(producto[0])
            resultado.append((producto, variantes))
        return resultado

    def buscar_productos(self, term=''):
        """
        Busca productos por descripción o código, incluyendo el padre si la variante coincide.
        """
        if not term.strip():
            return self.obtener_producto_con_variantes()
        
        term = term.lower()
        resultado = []
        productos_ya_agregados = set()
        
        productos = self.listar_productos()
        
        for producto in productos:
            prod_id, codigo, descripcion, *_ = producto
            
            producto_coincide = (term in descripcion.lower() or 
                                 term in str(codigo).lower())
            
            variantes = self.listar_variantes_por_producto(prod_id)
            
            variantes_coinciden = any(
                term in variante[2].lower() or term in str(variante[5]).lower()
                for variante in variantes
            )
            
            if (producto_coincide or variantes_coinciden) and prod_id not in productos_ya_agregados:
                resultado.append((producto, variantes))
                productos_ya_agregados.add(prod_id)
        
        return resultado

    # Movimientos y Reportes
    def registrar_movimiento(self, producto_id, tipo, cantidad):
        c = self.conn.cursor()
        fecha = datetime.now().isoformat()
        c.execute('INSERT INTO movimientos (producto_id, tipo, cantidad, fecha) VALUES (?, ?, ?, ?)',
                  (producto_id, tipo, cantidad, fecha))
        if tipo == 'venta':
            c.execute('UPDATE productos SET stock = stock - ? WHERE id=?', (cantidad, producto_id))
        else:
            c.execute('UPDATE productos SET stock = stock + ? WHERE id=?', (cantidad, producto_id))
        self.conn.commit()

    def productos_bajo_stock(self):
        c = self.conn.cursor()
        c.execute('SELECT id, codigo, descripcion, stock, minimo FROM productos WHERE stock <= minimo')
        return c.fetchall()

    # Importación/Exportación
    def export_to_excel(self, path):
        productos = self.listar_productos()
        df = pd.DataFrame(productos, columns=['id','codigo','descripcion','precio','stock','minimo','categoria','imagen','importado'])
        df.to_excel(path, index=False)

    def preview_excel_import(self, path):
        try:
            df = pd.read_excel(path, dtype=object, engine='openpyxl')
            preview_info = f"Filas a procesar: {len(df)}\n\n"
            preview_info += f"Columnas detectadas:\n{', '.join(df.columns)}\n\n"
            preview_info += "Primeras 3 filas:\n"
            for i in range(min(3, len(df))):
                primera_columna = str(df.iloc[i, 0]) if len(df.columns) > 0 else 'VACÍO'
                preview_info += f"Fila {i+1}: Primera columna = '{primera_columna}'\n"
            return preview_info
        except Exception as e:
            return f"Error al leer archivo: {str(e)}"

    def import_from_excel(self, path):
        try:
            try:
                df = pd.read_excel(path, dtype=object, engine='openpyxl')
                if all('unnamed' in str(col).lower() for col in df.columns):
                    df = pd.read_excel(path, dtype=object, engine='openpyxl', header=None)
                    column_names = ['codigo', 'descripcion', 'precio', 'stock', 'minimo', 'categoria', 'imagen']
                    df.columns = column_names[:len(df.columns)]
            except Exception:
                df = pd.read_excel(path, dtype=object, engine='openpyxl', header=None)
                column_names = ['codigo', 'descripcion', 'precio', 'stock', 'minimo', 'categoria', 'imagen']
                df.columns = column_names[:len(df.columns)]
        except Exception as e:
            print('Error import_from_excel read:', e)
            return 0, 1

        exitosos = 0
        errores = 0
        df.columns = [str(c).strip().lower() for c in df.columns]

        for index, row in df.iterrows():
            try:
                codigo = ''
                if 'codigo' in df.columns:
                    codigo = str(row.get('codigo', '')).strip() if not pd.isna(row.get('codigo', '')) else ''
                if not codigo and len(df.columns) > 0:
                    first_col = df.columns[0]
                    codigo = str(row.get(first_col, '')).strip() if not pd.isna(row.get(first_col, '')) else ''
                if not codigo:
                    codigo = f"IMP{index+1:04d}"

                descripcion = 'Sin descripción'
                if 'descripcion' in df.columns and not pd.isna(row.get('descripcion', '')):
                    descripcion = str(row.get('descripcion', '')).strip()
                elif len(df.columns) > 1 and not pd.isna(row.iloc[1] if len(row) > 1 else ''):
                    descripcion = str(row.iloc[1]).strip()

                precio = 0.0
                if 'precio' in df.columns:
                    precio = parse_number_safe(row.get('precio', 0), default=0.0)
                elif len(df.columns) > 2:
                    precio = parse_number_safe(row.iloc[2] if len(row) > 2 else 0, default=0.0)

                stock = parse_int_safe(row.get('stock', 0), default=0)
                minimo = parse_int_safe(row.get('minimo', 0), default=0)
                categoria = str(row.get('categoria', 'General')) if not pd.isna(row.get('categoria', '')) else 'General'
                imagen = ''

                ok = self.agregar_producto(codigo, descripcion, precio, stock, minimo, categoria, imagen, importado=1)
                if ok:
                    exitosos += 1
                else:
                    errores += 1
            except Exception as e:
                print(f"Error en fila {index}: {e}")
                errores += 1
                continue

        return exitosos, errores

    def aumentar_stock_por_importado(self, importado=1, cantidad=1):
        c = self.conn.cursor()
        c.execute('UPDATE productos SET stock = stock + ? WHERE COALESCE(importado,0)=?', (cantidad, importado))
        self.conn.commit()


# --- CLASE PRINCIPAL DE LA APLICACIÓN ---
class App:
    def __init__(self, root):
        self.root = root

        # Colores (primero)
        self.C_YELLOW = '#ffbf00'
        self.C_WHITE = '#ffffff'
        self.C_GRAY = '#3a3a3a'
        self.C_LIGHT_GRAY = '#f3f4f6'
        self.C_LIGHT_BLUE = '#e8f4fd'

        self.db = FerreteriaDB()
        self.images_cache = {}
        self.current_image_path = ""
        self.expanded_items = set()
        self.productos_factura = {}  # {codigo: (descripcion, precio, cantidad)}

        self.preview_label = tk.Label(
            self.root,
            text='Seleccione un producto',
            bg='#ffffff',
            bd=1,
            relief='solid',
            width=40,
            height=12
        )
        self.preview_label.place(x=50, y=600)  # Ajustá posición según tu layout
        self.current_image = None  # Mantener referencia de la imagen



        style = tb.Style(theme='cosmo')
        style.configure('Treeview', rowheight=50) 

        self.root.title('Sistema de Gestión - Ferretería con Variantes')
        self.root.geometry('1400x800')
        self.root.minsize(1200, 700)
        self.root.configure(bg=self.C_WHITE)

        # Crear layout (self.tree)
        self.create_main_layout()

        # Menú contextual
        self.menu_contextual = tb.Menu(self.root, tearoff=0)
        self.menu_contextual.add_command(label="Editar", command=self.abrir_producto_seleccionado)
        self.menu_contextual.add_command(label="Eliminar", command=self.eliminar_seleccion)

        # Bind click derecho en el tree
        self.tree.bind("<Button-3>", self.mostrar_menu_contextual)

        # Cargar productos y alerta de stock
        self.cargar_productos()
        self.alerta_bajo_stock()

    def mostrar_imagen_producto(self, producto):
        try:
            imagen_path = producto[7]  # Ruta de imagen del producto
            if not imagen_path or not os.path.exists(imagen_path):
                # Placeholder si no existe
                img = Image.new('RGB', (150, 100), (230, 230, 230))
            else:
                img = Image.open(imagen_path)

            # Redimensionar para preview
            alto_preview = 120
            proporcion = alto_preview / img.height
            nuevo_ancho = int(img.width * proporcion)
            img = img.resize((nuevo_ancho, alto_preview), Image.Resampling.LANCZOS)

            if img.mode in ('RGBA', 'LA'):
                background = Image.new('RGB', img.size, (255, 255, 255))
                background.paste(img, mask=img.split()[-1])
                img = background

            photo = ImageTk.PhotoImage(img)
            self.preview_label.configure(image=photo, text='')
            self.preview_label.image = photo  # Mantener referencia
            self.current_image = photo

        except Exception as e:
            print("Error mostrando imagen:", e)
            self.preview_label.configure(image='', text='Error cargando imagen')



    def abrir_producto_seleccionado(self):
        """Abre el formulario de edición del producto seleccionado en el Treeview."""
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Advertencia", "Seleccione un producto para editar.")
            return

        item = selection[0]
        iid = self.tree.item(item).get('iid', item)  # Usar IID explícito

        if not iid.startswith("P_"):
            messagebox.showwarning("Advertencia", "Seleccione un PRODUCTO, no una variante ni categoría.")
            return

        prod_id = int(iid.split("_")[1])

        # Obtener datos completos del producto
        producto = self.db.obtener_producto_por_id(prod_id)
        if not producto:
            messagebox.showerror("Error", "No se encontró el producto seleccionado.")
            return

        # Abrir formulario de edición
        self.abrir_form_editar_producto(*producto)





    def mostrar_menu_contextual(self, event):
        """Muestra el menú contextual al hacer click derecho sobre un item del Treeview."""
        iid = self.tree.identify_row(event.y)
        if iid:
            self.tree.selection_set(iid)  # Selecciona el item
            self.menu_contextual.post(event.x_root, event.y_root)

    def create_main_layout(self):
        main_frame = tb.Frame(self.root, padding=10, bootstyle=(PRIMARY, ''))
        main_frame.pack(fill=BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(main_frame, bg=self.C_YELLOW, bd=0, relief='flat')
        header_frame.pack(fill=X, pady=(0, 10))
        header_frame.pack_propagate(False)
        header_frame.configure(height=70)

        title = tk.Label(
            header_frame,
            text='  🏪  SISTEMA DE GESTIÓN - FERRETERÍA (CON VARIANTES)',
            font=('Helvetica', 16, 'bold'),
            bg=self.C_YELLOW,
            fg=self.C_GRAY
        )
        title.pack(side=LEFT, padx=20)

        # --- Vista del dólar ---
        self.dolar_label = tk.Label(
            header_frame,
            text='Cargando cotización...',
            font=('Helvetica', 11, 'bold'),
            bg=self.C_YELLOW,
            fg=self.C_GRAY
        )
        self.dolar_label.pack(side=RIGHT, padx=20)
        
        # Actualizar cada cierto tiempo (ej: cada 10 minutos)
        self.actualizar_dolar()

        # Controles principales
        controls_frame = tb.Frame(main_frame)
        controls_frame.pack(fill=X, pady=10)

        # Búsqueda
        search_frame = tb.Frame(controls_frame)
        search_frame.pack(side=LEFT, fill=X, expand=True)
        
        tk.Label(search_frame, text='🔍 Buscar:').pack(side=LEFT, padx=(0,5))
        self.search_var = tk.StringVar()
        search_entry = tb.Entry(search_frame, textvariable=self.search_var, width=30)
        search_entry.pack(side=LEFT, padx=5)
        search_entry.bind('<KeyRelease>', lambda e: self.cargar_productos())
        
        # Botones principales
        actions_frame = tb.Frame(controls_frame)
        actions_frame.pack(side=RIGHT)
        
        tb.Button(actions_frame, text='➕ Agregar Artículo', bootstyle=('warning', 'outline'),
                  command=self.abrir_agregar).pack(side=LEFT, padx=2)
        tb.Button(actions_frame, text='✏️ Editar Artículo', bootstyle=('info', 'outline'),
            command=self.abrir_producto_seleccionado).pack(side=LEFT, padx=2)
        tb.Button(actions_frame, text='🗑️ Eliminar', bootstyle=(DANGER, 'outline'),
                  command=self.eliminar_seleccion).pack(side=LEFT, padx=2)
        tb.Button(actions_frame, text='➕ Agregar a Factura', bootstyle=(SUCCESS, ''), 
                  command=self.agregar_a_factura).pack(side=LEFT, padx=2)
        tb.Button(actions_frame, text='💥 Eliminar Todos', bootstyle=(DARK, 'outline'),
                  command=self.eliminar_todos).pack(side=LEFT, padx=2)

        export_frame = tb.Frame(controls_frame, bootstyle="light")
        export_frame.pack(side=RIGHT, padx=20)
        
        tb.Button(export_frame, text='📤 Exportar Excel', bootstyle=(PRIMARY, ''), 
                  command=self.exportar_excel).pack(side=RIGHT, padx=2)
        tb.Button(export_frame, text='📥 Importar Excel', bootstyle=(SUCCESS, ''), 
                  command=self.importar_excel).pack(side=RIGHT, padx=2)
        

        # Contenido principal
        content_frame = tb.Frame(main_frame)
        content_frame.pack(fill=BOTH, expand=True)

        # Treeview a la izquierda
        tree_frame = tb.Frame(content_frame)
        tree_frame.pack(side=LEFT, fill=BOTH, expand=True, padx=(0, 10))

        # Treeview con scroll
        tree_container = tb.Frame(tree_frame)
        tree_container.pack(fill=BOTH, expand=True)

        # Configurar Treeview MEJORADO
        self.tree = ttk.Treeview(tree_container, columns=('codigo', 'precio', 'stock', 'minimo', 'categoria', 'importado'), 
                                 show='tree headings', height=25)
        
        self.tree.heading('#0', text='CATEGORÍA / DESCRIPCIÓN / VARIANTES')
        self.tree.column('#0', width=450, minwidth=350)
        
        self.tree.heading('codigo', text='CÓDIGO')
        self.tree.heading('precio', text='PRECIO')
        self.tree.heading('stock', text='STOCK')
        self.tree.heading('minimo', text='MÍNIMO')
        self.tree.heading('categoria', text='CATEGORÍA')
        self.tree.heading('importado', text='IMPORTADO')
        
        self.tree.column('codigo', width=100, anchor='center')
        self.tree.column('precio', width=100, anchor='center')
        self.tree.column('stock', width=80, anchor='center')
        self.tree.column('minimo', width=80, anchor='center')
        self.tree.column('categoria', width=120, anchor='center')
        self.tree.column('importado', width=90, anchor='center')

        # Scrollbar
        tree_scroll = ttk.Scrollbar(tree_container, orient=VERTICAL, command=self.tree.yview)
        self.tree.configure(yscrollcommand=tree_scroll.set)
        self.tree.pack(side=LEFT, fill=BOTH, expand=True)
        tree_scroll.pack(side=RIGHT, fill=Y)

        # Tags para diferentes niveles (Categoría añadido)
        self.tree.tag_configure('categoria', background=self.C_YELLOW, font=('Helvetica', 11, 'bold'))
        self.tree.tag_configure('producto', background=self.C_LIGHT_BLUE, font=('Helvetica', 10))
        self.tree.tag_configure('variante', background=self.C_WHITE, font=('Helvetica', 9))

        # Bind events
        self.tree.bind('<<TreeviewSelect>>', self.on_select)
        self.tree.bind('<Double-1>', self.on_double_click)

        # Sidebar a la derecha
        sidebar_frame = tb.Frame(content_frame, width=400)
        sidebar_frame.pack(side=RIGHT, fill=Y, padx=(10, 0))
        sidebar_frame.pack_propagate(False)

        # Acciones rápidas
        actions_card = tk.Frame(sidebar_frame, bg=self.C_WHITE, bd=1, relief='solid')
        actions_card.pack(fill=X, pady=5, padx=2)

        actions_title = tk.Label(actions_card, text='ACCIONES RÁPIDAS', font=('Helvetica', 12, 'bold'),
                                 bg=self.C_WHITE, fg=self.C_GRAY)
        actions_title.pack(pady=(6, 4))

        tb.Button(actions_card, text='📄 Exportar PDF', bootstyle=('primary', ''), 
                  command=self.exportar_pdf).pack(fill=X, pady=3, padx=8)
        tb.Button(actions_card, text='📊 Actualizar Precios', bootstyle=('warning', ''), 
                  command=self.actualizar_precios_masivo).pack(fill=X, pady=3, padx=8)
        tb.Button(actions_card, text='⚠️ Ver Stock Bajo', bootstyle=(DANGER, ''), 
                  command=self.mostrar_stock_bajo).pack(fill=X, pady=3, padx=8)
        tb.Button(actions_card, text='🔼 Aumentar stock', bootstyle=(INFO, ''), 
                  command=self.dialog_aumentar_stock_por_tipo).pack(fill=X, pady=3, padx=8)

        # Gestión de variantes
        variantes_card = tk.Frame(sidebar_frame, bg=self.C_WHITE, bd=1, relief='solid')
        variantes_card.pack(fill=X, pady=5, padx=2)

        variantes_title = tk.Label(variantes_card, text='GESTIÓN DE VARIANTES', font=('Helvetica', 12, 'bold'),
                                   bg=self.C_WHITE, fg=self.C_GRAY)
        variantes_title.pack(pady=(6, 4))

        variantes_buttons = tb.Frame(variantes_card)
        variantes_buttons.pack(fill=X, padx=8, pady=6)
        
        tb.Button(variantes_buttons, text='➕ Nueva Variante', bootstyle=(SUCCESS, ''), 
                  command=self.nueva_variante).pack(side=LEFT, padx=2)
        tb.Button(variantes_buttons, text='✏️ Editar Variante', bootstyle=(INFO, ''), 
                  command=self.editar_variante).pack(side=LEFT, padx=2)
        tb.Button(variantes_buttons, text='🗑️ Eliminar Variante', bootstyle=(DANGER, ''), 
                  command=self.eliminar_seleccion).pack(side=LEFT, padx=2)

        # Facturación
        invoice_card = tk.Frame(sidebar_frame, bg=self.C_WHITE, bd=1, relief='solid')
        invoice_card.pack(fill=BOTH, expand=True, pady=5, padx=2)

        invoice_title = tk.Label(invoice_card, text='FACTURA / CARRITO', font=('Helvetica', 12, 'bold'),
                                 bg=self.C_WHITE, fg=self.C_GRAY)
        invoice_title.pack(pady=(6, 4))

        inv_cols = ('codigo', 'descripcion', 'precio', 'cantidad', 'subtotal')
        self.invoice_tree = ttk.Treeview(invoice_card, columns=inv_cols, show='headings', height=6)
        for c in inv_cols:
            self.invoice_tree.heading(c, text=c.upper())
            self.invoice_tree.column(c, width=70)
        self.invoice_tree.column('descripcion', width=120)
        self.invoice_tree.pack(fill=BOTH, expand=True, padx=8)

        inv_buttons = tb.Frame(invoice_card)
        inv_buttons.pack(fill=X, padx=8, pady=6)
        tb.Button(inv_buttons, text='➕ Agregar', bootstyle=(SUCCESS, ''), 
                  command=self.agregar_a_factura).pack(side=LEFT, padx=2)
        tb.Button(inv_buttons, text='➖ Quitar', bootstyle=(DANGER, ''), 
                  command=self.quitar_linea_factura).pack(side=LEFT, padx=2)
        tb.Button(inv_buttons, text='🧾 Factura PDF', bootstyle=(PRIMARY, ''), 
                  command=self.generar_factura_pdf).pack(side=RIGHT, padx=2)

        total_frame = tb.Frame(invoice_card)
        total_frame.pack(fill=X, padx=8, pady=(0,10))
        self.total_var = tk.StringVar(value='Total: $0.00')
        tk.Label(total_frame, textvariable=self.total_var, font=('Helvetica', 12, 'bold'), 
                 bg=self.C_WHITE).pack(side=LEFT)
    
    # --- MÉTODOS DE VISUALIZACIÓN ---
    def cargar_productos(self):
        """Carga los productos y sus variantes en el treeview, agrupados por categoría."""
        
        # 1. Limpieza inicial
        for item in self.tree.get_children():
            self.tree.delete(item)
        self.images_cache.clear()

        # 2. Obtener productos y agrupar por categoría
        term = self.search_var.get().strip()
        productos_con_variantes = self.db.buscar_productos(term)
        
        productos_por_categoria = {}
        for producto, variantes in productos_con_variantes:
            categoria = producto[6] or 'Sin Categoría'
            if categoria not in productos_por_categoria:
                productos_por_categoria[categoria] = []
            productos_por_categoria[categoria].append((producto, variantes))

        sorted_categories = sorted(productos_por_categoria.keys())

        for categoria in sorted_categories:
            items = productos_por_categoria[categoria]
            
            # Insertar encabezado de categoría
            cat_item = self.tree.insert('', 'end', 
                                        text=f'  {categoria.upper()}', 
                                        tags=('categoria',), 
                                        open=True, 
                                        values=('', '', '', '', '', '')) 

            for producto, variantes in items:
                prod_id, codigo, descripcion, precio, stock, minimo, _, imagen_path, importado = producto # Renombrado a imagen_path para claridad

                # --- MANEJO DE IMAGEN PARA PRODUCTO PRINCIPAL ---
                
                # 3. Manejo de Placeholder
                if not imagen_path or not os.path.exists(imagen_path):
                    safe_name = "".join(c for c in descripcion.lower().replace(" ", "_") if c.isalnum() or c == '_')
                    safe_name = safe_name[:30]
                    placeholder_path = os.path.join(IMAGE_DIR, f'{safe_name}.png')
                    if not os.path.exists(placeholder_path):
                        create_placeholder_image(descripcion, placeholder_path)
                    imagen_path = placeholder_path

                # 4. Carga y Redimensionamiento
                photo = None
                try:
                    img = Image.open(imagen_path)
                    alto_fila = 50 
                    proporcion = alto_fila / img.height
                    nuevo_ancho = int(img.width * proporcion)
                    img = img.resize((nuevo_ancho, alto_fila), Image.Resampling.LANCZOS)
                    
                    if img.mode in ('RGBA', 'LA'):
                        background = Image.new('RGB', img.size, (255, 255, 255))
                        background.paste(img, mask=img.split()[-1])
                        img = background
                        
                    photo = ImageTk.PhotoImage(img)
                except:
                    placeholder = Image.new('RGB', (80, 50), (230, 230, 230))
                    photo = ImageTk.PhotoImage(placeholder)

                # 5. Insertar Producto Principal
                self.images_cache[prod_id] = photo
                importado_text = 'Sí' if importado == 1 else 'No'
                precio_text = f"${precio:.2f}" if precio else "$0.00"

                product_item = self.tree.insert(cat_item, 'end', 
                                                iid=f"P_{prod_id}",  # IID explícito
                                                text=f'  {descripcion}',
                                                image=photo, # <--- IMAGEN PRINCIPAL
                                                values=(codigo, precio_text, stock, minimo, categoria, importado_text),
                                                tags=('producto',))

                # --- INSERTAR VARIANTES (CON LA MISMA IMAGEN) ---
                for variante in variantes:
                    var_id, prod_id_var, desc_detallada, precio_var, stock_var, codigo_var = variante
                    precio_var_text = f"${precio_var:.2f}" if precio_var else "$0.00"
                    
                    variant_iid = f"V_{var_id}"
                    self.tree.insert(product_item, 'end',
                                    iid=variant_iid,  # IID explícito
                                    text=f'    • {desc_detallada}',
                                    image=photo, # <--- SE REUTILIZA LA IMAGEN DEL PADRE
                                    values=(codigo_var, precio_var_text, stock_var, '', '', ''),
                                    tags=('variante',))
                    
                    # CRUCIAL: Almacenar la referencia de la imagen para la variante
                    self.images_cache[variant_iid] = photo 

                # Expandir si tiene variantes
                if variantes:
                    self.tree.item(product_item, open=True)




    def on_select(self, event):
        selection = self.tree.selection()
        if not selection:
            return
        
        selected_iid = selection[0]
        
        # 1. Obtener el ID del producto (limpiando prefijos 'P_' o 'V_')
        if selected_iid.startswith('P_'):
            prod_id = selected_iid[2:]
        elif selected_iid.startswith('V_'):
            # Si es una variante, obtenemos el IID del padre (el producto principal)
            # Treeview.parent() devuelve el IID del padre.
            parent_iid = self.tree.parent(selected_iid)
            # El IID del padre debe ser 'P_ID', así que limpiamos el prefijo
            prod_id = parent_iid[2:]
        else:
            # Es una categoría o un ítem no válido para mostrar imagen.
            self.preview_label.configure(image='', text='Selecciona un producto o variante.')
            self.preview_label.image = None
            return

        # 2. Buscar la tupla completa del producto en la DB
        try:
            # Usamos el prod_id para buscar el producto completo (tupla de 9 elementos)
            producto = self.db.obtener_producto_por_id(prod_id)
            
            if producto:
                # 3. Llamar al método con la tupla de datos
                self.mostrar_imagen_producto(producto)
            else:
                self.preview_label.configure(image='', text='Producto no encontrado en DB.')
                self.preview_label.image = None

        except Exception as e:
            print(f"Error al obtener producto desde DB en on_select: {e}")
            self.preview_label.configure(image='', text='Error interno.')
            self.preview_label.image = None

    def editar_item_seleccionado(self):
        """Editar el producto seleccionado desde el Treeview."""
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Advertencia", "Seleccione un producto para editar.")
            return

        item = selection[0]
        tags = self.tree.item(item)['tags']

        # Solo permitir editar productos, no variantes ni categorías
        prod_iid = None
        if any(tag.startswith("P_") for tag in tags):
            prod_iid = next(tag for tag in tags if tag.startswith("P_"))
        else:
            messagebox.showwarning("Advertencia", "Seleccione un PRODUCTO para editar, no una variante ni categoría.")
            return

        prod_id = int(prod_iid.split("_")[1])

        # Obtener datos del producto
        producto = self.db.obtener_producto_por_id(prod_id)
        if not producto:
            messagebox.showerror("Error", "No se encontró el producto seleccionado.")
            return

        # Desempaquetar datos: (id, codigo, descripcion, precio, stock, minimo, categoria, imagen, importado)
        self.abrir_form_editar_producto(*producto)
        

    def abrir_form_editar_producto(self, prod_id, codigo, descripcion, precio, stock, minimo, categoria, imagen, importado):
        "Abre el formulario de edición del producto con los datos existentes."
        form_window = tb.Toplevel(self.root)
        form_window.title("Editar Producto")
        form_window.geometry("400x400")
        form_window.transient(self.root)
        form_window.grab_set()
        self.root.update_idletasks()  # Actualiza info de tamaño de root
        w = 400
        h = 400
        ws = self.root.winfo_width()
        hs = self.root.winfo_height()
        x = self.root.winfo_x() + (ws // 2) - (w // 2)
        y = self.root.winfo_y() + (hs // 2) - (h // 2)
        form_window.geometry(f"{w}x{h}+{x}+{y}")
        main_frame = tb.Frame(form_window, padding=20)
        main_frame.pack(fill=BOTH, expand=True)
        fields = [
            ("codigo", "Código:"),
            ("descripcion", "Descripción:"),
            ("precio", "Precio:"),
            ("stock", "Stock:"),
            ("minimo", "Stock Mínimo:"),
            ("categoria", "Categoría:"),
            ("importado", "Importado (0=No,1=Sí):")
        ]
        entries = {}
        values = [codigo, descripcion, precio, stock, minimo, categoria, importado]
        for i, (key, label) in enumerate(fields):
            row_frame = tb.Frame(main_frame)
            row_frame.pack(fill=X, pady=5)
            tk.Label(row_frame, text=label, width=18, anchor="w").pack(side=LEFT)
            entry = tb.Entry(row_frame)
            entry.pack(side=LEFT, fill=X, expand=True)
            entry.insert(0, str(values[i]))
            entries[key] = entry
        def guardar_cambios():
            try:
                codigo_n = entries["codigo"].get()
                descripcion_n = entries["descripcion"].get()
                precio_n = float(entries["precio"].get())
                stock_n = int(entries["stock"].get())
                minimo_n = int(entries["minimo"].get())
                categoria_n = entries["categoria"].get()
                importado_n = int(entries["importado"].get())
            except ValueError:
                messagebox.showerror("Error", "Verifique los campos numéricos.")
                return

            self.db.editar_producto(prod_id, codigo_n, descripcion_n, precio_n, stock_n, minimo_n, categoria_n, imagen, importado_n)
            form_window.destroy()
            self.cargar_productos()
            messagebox.showinfo("Éxito", "Producto editado correctamente.")

        tb.Button(main_frame, text="Guardar", command=guardar_cambios, bootstyle=(SUCCESS, "solid")).pack(fill=X, pady=10)
        tb.Button(main_frame, text="Cancelar", command=form_window.destroy, bootstyle=(DANGER, "outline")).pack(fill=X)

    def on_double_click(self, event):
        """Expande/contrae al hacer doble click"""
        item = self.tree.identify_row(event.y)
        if not item:
            return
        
        tags = self.tree.item(item)['tags']
        if 'producto' in tags or 'categoria' in tags:
            if self.tree.item(item)['open']:
                self.tree.item(item, open=False)
            else:
                self.tree.item(item, open=True)

   
    def abrir_agregar(self):
        self.abrir_form()

    def abrir_editar(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning('Advertencia', 'Por favor, seleccione un ítem para editar.')
            return
        
        item = selection[0] # Este es el IID ('P_ID' o 'V_ID')
        
        # 1. INTENTAR EDITAR VARIANTE
        if item.startswith('p_'):
            # Si es una Variante, extraemos el ID y llamamos a la función de edición de VARIANTE.
            prod_id = item[2:]
            self.abrir_ventana_edicion(item_id=prod_id, item_type='producto')
            return
            
        # 2. INTENTAR EDITAR PRODUCTO PRINCIPAL
        elif item.startswith('P_'):
            # Si es un Producto, extraemos el ID y llamamos a la función de edición de PRODUCTO.
            prod_id = item[2:]
            self.abrir_ventana_edicion(item_id=prod_id, item_type='producto')
            return
                
        # 3. ÍTEM NO VÁLIDO (Categoría o Fila Vacía)
        else:
            # Usamos los tags como fallback si el IID no tiene el formato esperado, 
            # o si es una categoría, que no tiene tags de P_ o V_.
            tags = self.tree.item(item)['tags']
            if 'categoria' in tags:
                messagebox.showwarning('Advertencia', 'No se puede editar una categoría directamente.')
                return
            
        messagebox.showwarning('Advertencia', 'Por favor, seleccione un producto o una variante para editar.')

    def abrir_form(self, producto=None):
        form_window = tb.Toplevel(self.root)
        form_window.title('Agregar Producto' if not producto else 'Editar Producto')
        form_window.geometry('520x520')
        form_window.resizable(False, False)
        form_window.transient(self.root)
        form_window.grab_set()

        main_frame = tb.Frame(form_window, padding=20)
        main_frame.pack(fill=BOTH, expand=True)

        fields = [
            ('codigo', 'Código:', 'text'),
            ('descripcion', 'Descripción:', 'text'),
            ('precio', 'Precio:', 'number'),
            ('stock', 'Stock:', 'number'),
            ('minimo', 'Stock Mínimo:', 'number'),
            ('categoria', 'Categoría:', 'text')
        ]

        entries = {}
        imagen_path = tk.StringVar(value='')
        current_image_tk = None
        producto_id = producto[0] if producto else None

        for i, (key, label, type) in enumerate(fields):
            row_frame = tb.Frame(main_frame)
            row_frame.pack(fill=X, pady=5)
            
            tk.Label(row_frame, text=label, width=15, anchor='w').pack(side=LEFT)
            entry = tb.Entry(row_frame)
            entry.pack(side=LEFT, fill=X, expand=True)
            entries[key] = entry
            
            if producto:
                # 1=codigo, 2=descripcion, 3=precio, 4=stock, 5=minimo, 6=categoria, 7=imagen
                entry.insert(0, producto[i+1]) 
                if key == 'precio' or key == 'stock' or key == 'minimo':
                    entry.delete(0, END)
                    entry.insert(0, str(producto[i+1]))
            
        # Campo de Imagen
        img_frame = tb.Frame(main_frame)
        img_frame.pack(fill=X, pady=10)
        tk.Label(img_frame, text='Imagen:', width=15, anchor='w').pack(side=LEFT)
        
        img_preview_label = tk.Label(img_frame, width=30, height=2, bg=self.C_LIGHT_GRAY, text="Sin imagen seleccionada")
        img_preview_label.pack(side=LEFT, padx=5, fill=X, expand=True)

        def seleccionar_imagen():
            nonlocal current_image_tk
            path = filedialog.askopenfilename(filetypes=[("Image files", "*.jpg *.jpeg *.png")])
            if path:
                imagen_path.set(path)
                img_preview_label.config(text=os.path.basename(path))
                
                # Mostrar thumbnail en el label
                try:
                    img = Image.open(path)
                    img.thumbnail((40, 40), Image.Resampling.LANCZOS)
                    if img.mode in ('RGBA', 'LA'):
                        background = Image.new('RGB', img.size, (255, 255, 255))
                        background.paste(img, mask=img.split()[-1])
                        img = background
                    current_image_tk = ImageTk.PhotoImage(img)
                    img_preview_label.config(image=current_image_tk, compound='left', text=os.path.basename(path))
                    img_preview_label.image = current_image_tk
                except Exception:
                    img_preview_label.config(image='', text=os.path.basename(path))


        tb.Button(img_frame, text='Seleccionar', command=seleccionar_imagen).pack(side=RIGHT)

        if producto and producto[7]:
            imagen_path.set(producto[7])
            seleccionar_imagen() # Para cargar el preview

        def guardar():
            codigo = entries['codigo'].get().strip()
            descripcion = entries['descripcion'].get().strip()
            
            if not codigo or not descripcion:
                messagebox.showerror('Error', 'El código y la descripción son obligatorios')
                return

            try:
                precio = parse_number_safe(entries['precio'].get())
                stock = parse_int_safe(entries['stock'].get())
                minimo = parse_int_safe(entries['minimo'].get())
            except ValueError:
                messagebox.showerror('Error', 'Precio, Stock y Mínimo deben ser números válidos.')
                return

            categoria = entries['categoria'].get().strip()
            
            final_image_path = ""
            if imagen_path.get():
                try:
                    ext = os.path.splitext(imagen_path.get())[1].lower()
                    if ext not in ['.jpg', '.jpeg', '.png']:
                        ext = '.png'
                    
                    # Usar el código y la descripción para el nombre de archivo
                    clean_name = codigo if codigo else descripcion.replace(" ", "_")
                    clean_name = "".join(c for c in clean_name if c.isalnum() or c in ('_', '-'))
                    
                    final_image_name = f"{clean_name}{ext}"
                    final_image_path = os.path.join(IMAGE_DIR, final_image_name)
                    
                    # Copiar y renombrar la imagen
                    if imagen_path.get() != final_image_path:
                        shutil.copy2(imagen_path.get(), final_image_path)
                    
                except Exception as e:
                    messagebox.showwarning('Advertencia', f'No se pudo guardar la imagen: {e}')
                    final_image_path = imagen_path.get() # Intentar guardar la ruta original
                    

            if producto_id:
                self.db.editar_producto(producto_id, codigo, descripcion, precio, stock, minimo, categoria, final_image_path)
            else:
                self.db.agregar_producto(codigo, descripcion, precio, stock, minimo, categoria, final_image_path)

            form_window.destroy()
            self.cargar_productos()

        tb.Button(main_frame, text='Guardar' if producto else 'Agregar', command=guardar, bootstyle=(SUCCESS, 'solid')).pack(fill=X, pady=10)
        tb.Button(main_frame, text='Cancelar', command=form_window.destroy, bootstyle=(DANGER, 'outline')).pack(fill=X)

    def eliminar_seleccion(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning('Advertencia', 'Seleccione un producto o variante para eliminar.')
            return

        item = selection[0]
        iid = self.tree.item(item).get('iid', item)

        # --- VARIANTE ---
        if iid.startswith('V_'):
            var_id = int(iid.split('_')[1])
            confirm = messagebox.askyesno('Confirmar', f'¿Está seguro de eliminar esta VARIANTE (ID: {var_id})?')
            if confirm:
                self.db.eliminar_variante(var_id)
                self.cargar_productos()
            return

        # --- PRODUCTO ---
        elif iid.startswith('P_'):
            prod_id = int(iid.split('_')[1])
            confirm = messagebox.askyesno(
                'Confirmar',
                f'¿Está seguro de eliminar este PRODUCTO y TODAS sus variantes asociadas (ID: {prod_id})?'
            )
            if confirm:
                self.db.eliminar_producto(prod_id)
                self.cargar_productos()
            return

        # --- CATEGORÍA ---
        elif 'categoria' in self.tree.item(item)['tags']:
            messagebox.showwarning('Advertencia', 'No se puede eliminar una categoría directamente. Seleccione un producto.')
            return

        else:
            messagebox.showwarning('Advertencia', 'Elemento seleccionado no reconocido.')



    def eliminar_todos(self):
        confirm = messagebox.askyesno('Confirmar', '¡CUIDADO! ¿Está seguro de ELIMINAR TODOS los productos y sus variantes de la base de datos?')
        if confirm:
            self.db.conn.cursor().execute('DELETE FROM variantes')
            self.db.conn.cursor().execute('DELETE FROM productos')
            self.db.conn.commit()
            messagebox.showinfo('Éxito', 'Todos los productos han sido eliminados.')
            self.cargar_productos()

    # --- MÉTODOS DE GESTIÓN DE VARIANTES ---
    def obtener_producto_seleccionado_id(self):
        selection = self.tree.selection()
        if not selection:
            return None, None
        
        item = selection[0]
        tags = self.tree.item(item)['tags']
        
        # Si selecciona una variante, obtenemos el padre (producto principal)
        if 'variante' in tags:
            item = self.tree.parent(item)
            tags = self.tree.item(item)['tags']
            
        if 'producto' in tags:
            codigo = self.tree.item(item)['values'][0]
            product = self.db.obtener_producto_por_codigo(codigo)
            return product[0], product[2] # Retorna (id, descripcion)
        return None, None
    
    def nueva_variante(self):
        prod_id, prod_desc = self.obtener_producto_seleccionado_id()
        if not prod_id:
            messagebox.showwarning('Advertencia', 'Por favor, seleccione un producto (no una variante ni categoría) para añadirle una nueva variante.')
            return
        
        self.abrir_form_variante(prod_id=prod_id, prod_desc=prod_desc)

    def editar_variante(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning('Advertencia', 'Seleccione una variante para editar.')
            return

        item = selection[0] # item YA ES el IID, ej: 'V_456'
        tags = self.tree.item(item)['tags']

        if 'variante' not in tags:
            messagebox.showwarning('Advertencia', 'Debe seleccionar una VARIANTE (la línea con el punto •) para editarla.')
            return

        # --- OBTENCIÓN CORRECTA DEL VAR_ID ---
        
        # 1. 'item' ya contiene el IID (ej: 'V_456')
        iid = item 

        if not iid.startswith('V_'):
            messagebox.showwarning('Error', 'No se pudo identificar la variante seleccionada.')
            return

        try:
            # 2. Extraer el número y convertir a entero.
            var_id = int(iid.split('_')[1])
        except (IndexError, ValueError):
            messagebox.showerror('Error', 'El formato del ID de la variante es incorrecto.')
            return
        
        # --- CONTINUACIÓN DE LA LÓGICA ---

        # Obtener datos de la variante
        variante = self.db.obtener_variante_por_id(var_id)
        if variante:
            prod_id = variante[1]
            prod = self.db.obtener_producto_por_id(prod_id)
            prod_desc = prod[2] if prod else "Producto desconocido"

            # Abrir formulario para editar variante (Asegúrate que esta función exista)
            self.abrir_form_variante(variante=variante, prod_id=prod_id, prod_desc=prod_desc)
        else:
            messagebox.showerror('Error', f'No se encontraron datos para la variante ID: {var_id}.')

    def abrir_form_variante(self, prod_id, prod_desc, variante=None):
        form_window = tb.Toplevel(self.root)
        form_window.title('Agregar Variante' if not variante else 'Editar Variante')
        form_window.geometry('400x300')
        form_window.resizable(False, False)
        form_window.transient(self.root)
        form_window.grab_set()

        main_frame = tb.Frame(form_window, padding=20)
        main_frame.pack(fill=BOTH, expand=True)
        
        tk.Label(main_frame, text=f"Producto Principal: {prod_desc}", font=('Helvetica', 10, 'bold')).pack(fill=X, pady=5)
        
        fields = [
            ('codigo', 'Código (Opcional):'),
            ('descripcion_detallada', 'Descripción Detallada:'),
            ('precio', 'Precio:'),
            ('stock', 'Stock:')
        ]

        entries = {}
        variante_id = variante[0] if variante else None

        for i, (key, label) in enumerate(fields):
            row_frame = tb.Frame(main_frame)
            row_frame.pack(fill=X, pady=5)
            
            tk.Label(row_frame, text=label, width=18, anchor='w').pack(side=LEFT)
            entry = tb.Entry(row_frame)
            entry.pack(side=LEFT, fill=X, expand=True)
            entries[key] = entry
            
            if variante:
                # 0=id, 1=prod_id, 2=desc_detallada, 3=precio, 4=stock, 5=codigo
                val_index = 5 if key == 'codigo' else (2 + i)
                entry.insert(0, str(variante[val_index]))
        
        def guardar_variante():
            desc_detallada = entries['descripcion_detallada'].get().strip()
            codigo = entries['codigo'].get().strip()
            
            if not desc_detallada:
                messagebox.showerror('Error', 'La descripción detallada es obligatoria.')
                return
            
            try:
                precio = parse_number_safe(entries['precio'].get())
                stock = parse_int_safe(entries['stock'].get())
            except ValueError:
                messagebox.showerror('Error', 'Precio y Stock deben ser números válidos.')
                return

            if variante_id:
                self.db.editar_variante(variante_id, desc_detallada, precio, stock, codigo)
            else:
                self.db.agregar_variante(prod_id, desc_detallada, precio, stock, codigo)

            form_window.destroy()
            self.cargar_productos()

        tb.Button(main_frame, text='Guardar' if variante else 'Agregar', command=guardar_variante, bootstyle=(SUCCESS, 'solid')).pack(fill=X, pady=10)
        tb.Button(main_frame, text='Cancelar', command=form_window.destroy, bootstyle=(DANGER, 'outline')).pack(fill=X)


    # --- MÉTODOS DE FACTURACIÓN ---
    def agregar_a_factura(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning('Advertencia', 'Seleccione un producto o variante para agregar a la factura.')
            return
        
        item = selection[0]
        tags = self.tree.item(item)['tags']
        values = self.tree.item(item)['values']
        
        if 'categoria' in tags:
            messagebox.showwarning('Advertencia', 'No puede facturar una categoría.')
            return

        codigo = values[0]
        precio_str = values[1].replace('$', '').replace(',', '')
        
        # Determinar si es un producto principal o una variante
        if 'producto' in tags:
            descripcion = self.tree.item(item)['text'].strip()
        elif 'variante' in tags:
            descripcion = self.tree.item(item)['text'].strip().lstrip('• ')
        else:
            messagebox.showwarning('Advertencia', 'Selección no válida.')
            return

        cantidad = simpledialog.askinteger("Cantidad", f"Cantidad para '{descripcion}' ({codigo}):", initialvalue=1, minvalue=1)
        
        if cantidad:
            precio = parse_number_safe(precio_str)
            subtotal = precio * cantidad
            
            if codigo in self.productos_factura:
                # Si ya existe, sumar la cantidad
                desc, old_precio, old_cantidad = self.productos_factura[codigo]
                self.productos_factura[codigo] = (descripcion, precio, old_cantidad + cantidad)
            else:
                self.productos_factura[codigo] = (descripcion, precio, cantidad)
            
            self.actualizar_factura_treeview()

    def quitar_linea_factura(self):
        selected_item = self.invoice_tree.selection()
        if not selected_item:
            messagebox.showwarning('Advertencia', 'Seleccione una línea de la factura para quitar.')
            return
        
        # El código está en la primera columna del Treeview de la factura
        codigo = self.invoice_tree.item(selected_item[0], 'values')[0]
        
        if codigo in self.productos_factura:
            del self.productos_factura[codigo]
            self.actualizar_factura_treeview()

    def actualizar_factura_treeview(self):
        for item in self.invoice_tree.get_children():
            self.invoice_tree.delete(item)
        
        total = 0.0
        
        for codigo, (descripcion, precio, cantidad) in self.productos_factura.items():
            subtotal = precio * cantidad
            total += subtotal
            
            self.invoice_tree.insert('', 'end', values=(
                codigo,
                descripcion[:20] + '...' if len(descripcion) > 23 else descripcion,
                f"${precio:.2f}",
                cantidad,
                f"${subtotal:.2f}"
            ))
            
        self.total_var.set(f"Total: ${total:.2f}")

    def generar_factura_pdf(self):
        if not self.productos_factura:
            messagebox.showwarning('Advertencia', 'El carrito de facturación está vacío.')
            return

        filename = filedialog.asksaveasfilename(
            defaultextension=".pdf",
            filetypes=[("PDF files", "*.pdf")],
            initialfile=f"Factura_{datetime.now().strftime('%Y%m%d_%H%M%S')}.pdf"
        )
        if not filename:
            return

        c = canvas.Canvas(filename, pagesize=A4)
        width, height = A4

        # Encabezado
        c.setFont('Helvetica-Bold', 16)
        c.drawString(50, height - 50, 'FACTURA DE VENTA - FERRETERÍA')

        c.setFont('Helvetica', 10)
        c.drawString(50, height - 70, f"Fecha: {datetime.now().strftime('%d/%m/%Y %H:%M:%S')}")

        total_a_facturar = sum(precio * cantidad for _, (descripcion, precio, cantidad) in self.productos_factura.items())
        c.drawString(50, height - 85, f"Total a Pagar: ${total_a_facturar:.2f}")

        # Detalles
        y_start = height - 120
        c.setFont('Helvetica-Bold', 10)
        headers = ['CÓDIGO', 'DESCRIPCIÓN', 'PRECIO UNITARIO', 'CANTIDAD', 'SUBTOTAL']
        col_widths = [80, 200, 100, 80, 100]
        x_pos = 50
        for i, header in enumerate(headers):
            c.drawString(x_pos, y_start, header)
            x_pos += col_widths[i]

        y = y_start - 20
        c.setFont('Helvetica', 9)

        for codigo, (descripcion, precio, cantidad) in self.productos_factura.items():
            subtotal = precio * cantidad
            x_pos = 50
            c.drawString(x_pos, y, str(codigo))
            x_pos += col_widths[0]
            c.drawString(x_pos, y, descripcion[:35] + '...' if len(descripcion) > 38 else descripcion)
            x_pos += col_widths[1]
            c.drawString(x_pos, y, f"${precio:.2f}")
            x_pos += col_widths[2]
            c.drawString(x_pos, y, str(cantidad))
            x_pos += col_widths[3]
            c.drawString(x_pos, y, f"${subtotal:.2f}")

            # --- DESCONTAR STOCK Y REGISTRAR VENTA ---
            producto_db = self.db.obtener_producto_por_codigo(codigo)
            if producto_db:
                prod_id = producto_db[0]
                stock_actual = producto_db[4]  # suponiendo que columna 4 es stock
                nuevo_stock = max(0, stock_actual - cantidad)
                self.db.actualizar_stock(prod_id, nuevo_stock)
                self.db.registrar_movimiento(prod_id, 'venta', cantidad)

            y -= 15
            if y < 50:  # Nueva página si llegamos al final
                c.showPage()
                y = height - 50
                c.setFont('Helvetica-Bold', 10)
                x_pos = 50
                for i, header in enumerate(headers):
                    c.drawString(x_pos, y, header)
                    x_pos += col_widths[i]
                y -= 20
                c.setFont('Helvetica', 9)

        # Total
        c.setLineWidth(1)
        c.line(450, 80, 560, 80)
        c.setFont('Helvetica-Bold', 12)
        c.drawString(400, 60, "TOTAL:")
        c.drawString(470, 60, f"${total_a_facturar:.2f}")

        c.save()
        messagebox.showinfo('Éxito', f'Factura generada correctamente en:\n{filename}')

        # Limpiar carrito y recargar productos
        self.productos_factura.clear()
        self.actualizar_factura_treeview()
        self.cargar_productos()



    # --- MÉTODOS DE REPORTES Y ACCIONES RÁPIDAS ---
    def alerta_bajo_stock(self):
        productos = self.db.productos_bajo_stock()
        if productos:
            msg = "¡Alerta de Stock Bajo!\nLos siguientes productos están por debajo de su stock mínimo:\n\n"
            for id, codigo, desc, stock, min_stock in productos:
                msg += f"Código: {codigo} - {desc} (Stock: {stock} / Mínimo: {min_stock})\n"
            messagebox.showwarning("Alerta de Stock", msg)

    def mostrar_stock_bajo(self):
        productos = self.db.productos_bajo_stock()
        if not productos:
            messagebox.showinfo('Stock', 'No hay productos bajo el stock mínimo.')
            return

        stock_window = tb.Toplevel(self.root)
        stock_window.title('Productos Bajo Stock Mínimo')
        stock_window.geometry('600x400')
        stock_window.transient(self.root)
        stock_window.grab_set()

        tree = ttk.Treeview(stock_window, columns=('codigo', 'descripcion', 'stock', 'minimo'), show='headings')
        tree.heading('codigo', text='CÓDIGO')
        tree.heading('descripcion', text='DESCRIPCIÓN')
        tree.heading('stock', text='STOCK ACTUAL')
        tree.heading('minimo', text='STOCK MÍNIMO')

        for id, codigo, desc, stock, min_stock in productos:
            tree.insert('', 'end', values=(codigo, desc, stock, min_stock))

        tree.pack(fill=BOTH, expand=True, padx=10, pady=10)

    def exportar_pdf(self):
        filename = filedialog.asksaveasfilename(defaultextension=".pdf", 
                                                filetypes=[("PDF files", "*.pdf")],
                                                initialfile=f"Reporte_Stock_{datetime.now().strftime('%Y%m%d')}.pdf")
        if not filename:
            return

        c = canvas.Canvas(filename, pagesize=A4)
        width, height = A4
        c.setFont('Helvetica-Bold', 16)
        c.drawString(50, height - 50, 'REPORTE DE STOCK - FERRETERÍA')
        c.setFont('Helvetica', 10)
        c.drawString(50, height - 70, f"Generado el: {datetime.now().strftime('%d/%m/%Y %H:%M:%S')}")
        
        y = height - 120
        c.setFont('Helvetica-Bold', 10)
        headers = ['CÓDIGO', 'DESCRIPCIÓN', 'PRECIO', 'STOCK', 'MÍNIMO', 'CATEGORÍA']
        col_widths = [70, 180, 80, 60, 60, 100]
        x_pos = 50
        for i, header in enumerate(headers):
            c.drawString(x_pos, y, header)
            x_pos += col_widths[i]
        
        y -= 15
        c.setFont('Helvetica', 8)
        
        for producto, variantes in self.db.obtener_producto_con_variantes():
            prod_id, codigo, descripcion, precio, stock, minimo, categoria, _, _ = producto
            
            items_to_print = [(codigo, descripcion, precio, stock, minimo, categoria)]
            for var in variantes:
                # id, producto_id, descripcion_detallada, precio, stock, codigo
                items_to_print.append((var[5], f" • {var[2]}", var[3], var[4], '', categoria)) 

            for item in items_to_print:
                x_pos = 50
                c.drawString(x_pos, y, str(item[0]))
                x_pos += col_widths[0]
                c.drawString(x_pos, y, str(item[1]))
                x_pos += col_widths[1]
                c.drawString(x_pos, y, f"${item[2]:.2f}" if item[2] else "$0.00")
                x_pos += col_widths[2]
                c.drawString(x_pos, y, str(item[3]))
                x_pos += col_widths[3]
                c.drawString(x_pos, y, str(item[4]))
                x_pos += col_widths[4]
                c.drawString(x_pos, y, str(item[5]))
            
                y -= 12
                if y < 50:
                    c.showPage()
                    y = height - 50
                    c.setFont('Helvetica-Bold', 10)
                    x_pos = 50
                    for i, header in enumerate(headers):
                        c.drawString(x_pos, y, header)
                        x_pos += col_widths[i]
                    y -= 15
                    c.setFont('Helvetica', 8)
        
        c.save()
        messagebox.showinfo('Éxito', f'Reporte de stock generado en:\n{filename}')
        
    def exportar_excel(self):
        filename = filedialog.asksaveasfilename(defaultextension=".xlsx", 
                                                filetypes=[("Excel files", "*.xlsx")],
                                                initialfile=f"Exportacion_Stock_{datetime.now().strftime('%Y%m%d')}.xlsx")
        if filename:
            self.db.export_to_excel(filename)
            messagebox.showinfo('Éxito', f'Datos exportados a Excel en:\n{filename}')

    def importar_excel(self):
        path = filedialog.askopenfilename(filetypes=[("Excel files", "*.xlsx *.xls")])
        if not path:
            return

        preview_info = self.db.preview_excel_import(path)
        
        confirm = messagebox.askyesno("Confirmar Importación", 
                                     f"Se detectó el siguiente contenido:\n\n{preview_info}\n\n¿Desea continuar con la importación y SOBREESCRIBIR productos existentes por CÓDIGO?")
        
        if confirm:
            exitosa, fallida = self.db.import_from_excel(path)
            messagebox.showinfo('Importación Finalizada', 
                                f'Importación completada:\n- Registros actualizados/insertados: {exitosa}\n- Errores/Ignorados: {fallida}')
            self.cargar_productos()

    def actualizar_precios_masivo(self):
        dialog = simpledialog.askstring("Actualizar Precios", "¿Desea aplicar un AUMENTO o una DISMINUCIÓN? (Escriba 'A' o 'D')")
        if not dialog:
            return

        dialog = dialog.strip().upper()
        if dialog not in ('A', 'D'):
            messagebox.showerror('Error', 'Opción no válida. Debe ser "A" o "D".')
            return

        porcentaje_str = simpledialog.askstring("Actualizar Precios", "Ingrese el porcentaje a aplicar (ej: 15 para 15%):")
        if not porcentaje_str:
            return

        try:
            porcentaje = float(porcentaje_str) / 100.0
            if porcentaje < 0:
                raise ValueError
        except ValueError:
            messagebox.showerror('Error', 'Porcentaje no válido.')
            return

        confirm = messagebox.askyesno('Confirmar', f"¿Está seguro de aplicar un {'AUMENTO' if dialog == 'A' else 'DISMINUCIÓN'} del {porcentaje*100:.2f}% a TODOS los precios?")
        if not confirm:
            return

        c = self.db.conn.cursor()
        if dialog == 'A':
            c.execute('UPDATE productos SET precio = precio * (?)', (1 + porcentaje,))
            c.execute('UPDATE variantes SET precio = precio * (?)', (1 + porcentaje,))
        else:
            c.execute('UPDATE productos SET precio = precio * (?)', (1 - porcentaje,))
            c.execute('UPDATE variantes SET precio = precio * (?)', (1 - porcentaje,))
            
        self.db.conn.commit()
        self.cargar_productos()
        messagebox.showinfo('Éxito', 'Precios actualizados correctamente.')

    def dialog_aumentar_stock_por_tipo(self):
        tipo_importado = simpledialog.askstring("Aumentar Stock", "¿A qué tipo de producto desea aumentar el stock?\n(Escriba '1' para Importado, '0' para Nacional)")
        if not tipo_importado or tipo_importado not in ('0', '1'):
            if tipo_importado is not None:
                messagebox.showerror('Error', 'Opción no válida. Use "0" o "1".')
            return
        
        cantidad_str = simpledialog.askstring("Aumentar Stock", f"Ingrese la cantidad a sumar a los productos tipo {'Importado' if tipo_importado == '1' else 'Nacional'}:", initialvalue=1)
        if not cantidad_str:
            return
        
        try:
            cantidad = int(cantidad_str)
        except ValueError:
            messagebox.showerror('Error', 'Cantidad no válida.')
            return
        
        confirm = messagebox.askyesno('Confirmar', f"¿Está seguro de SUMAR {cantidad} unidades al stock de TODOS los productos tipo {'Importado' if tipo_importado == '1' else 'Nacional'}?")
        if confirm:
            self.db.aumentar_stock_por_importado(importado=int(tipo_importado), cantidad=cantidad)
            self.cargar_productos()
            messagebox.showinfo('Éxito', 'Stock actualizado correctamente.')

    def actualizar_dolar(self):
        """Actualiza el valor del dólar cada 10 minutos sin threading."""
        try:
            oficial, blue = obtener_dolares()
            texto = f"💵 Dólar Oficial: ${oficial:.2f} | Blue: ${blue:.2f}" if oficial and blue else "⚠️ No se pudo actualizar la cotización."
        except Exception:
            texto = "⚠️ Error al obtener la cotización."
        
        self.dolar_label.config(text=texto)
        
        # Reprogramar la próxima actualización en 10 minutos (600000 ms)
        self.root.after(600000, self.actualizar_dolar)
# --- INICIO DE LA APLICACIÓN ---
if __name__ == '__main__':
    root = tb.Window(themename='cosmo')
    app = App(root)
    root.mainloop()
