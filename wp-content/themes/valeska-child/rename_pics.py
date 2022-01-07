import shutil
import os
import glob

def listdir_nohidden(path):
    return glob.glob(os.path.join(path,'*'))

def copy_files_from_source_to_destination(source, destination):
    # code to move the files from sub-folder to main folder.
    files = os.listdir(source)
    for file in files:
        file_name = os.path.join(source, file)
        shutil.copy(file_name, destination)
    print("Files Moved")

def sposta_tutti(cartella_archivio, cartella_all_products):
    cartelle_prodotto = listdir_nohidden(cartella_archivio)
    for cartella_prodotto in cartelle_prodotto:
        cartelle_colore = listdir_nohidden(cartella_prodotto)
        for cartella_colore in cartelle_colore:
            copy_files_from_source_to_destination(cartella_colore, cartella_all_products)

def rename_files(path, rules):
    files = glob.glob(os.path.join(path,'*'))
    for file in files:
        filename = os.path.basename(file)
        print(filename)
        new_filename = filename
        for key in rules:
            new_filename = new_filename.replace(key, rules[key])
        print(new_filename)
        os.rename(os.path.join(path, filename), os.path.join(path, new_filename))



cartella_archivio = '/Users/marcoferrera/Desktop/PDP'
cartella_all_products = '/Users/marcoferrera/Desktop/prodottiarchivio'
#sposta_tutti(cartella_archivio, cartella_all_products):
rules = {' ':'_', 'Still':'0'}
rename_files(cartella_all_products, rules)