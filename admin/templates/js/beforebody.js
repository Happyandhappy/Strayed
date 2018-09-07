function p(ulaz){
	var d={};
	
	var en={};
	en["Odjavi se"]="Sign out";
	en["Dodaj novi zapis"]="Add new record";
	en["Zatvori"]="Close";
	en["Sačuvaj promene"]="Save changes";
	en["Izmeni zapis"]="Edit record";
	en["Dodaj predefinisan zapis"]="Add predefined record";
	en["Resetuj prikaz"]="Reset view";
	en["Dodaj"]="Add";
	en["Duplikat"]="Duplicate";
	en["Izmeni"]="Edit";
	en["Obriši"]="Delete";
	en["Komentari"]="Comments";
	en["Detalji"]="Details";
	en["Izvoz"]="Export";
	en["Štampa"]="Print";
	en["Broj zapisa"]="Number of records";
	en["Kolone"]="Columns";
	en["Odaberi"]="Choice";
	en["Ništa nije odabrano"]="No selected value";
	en["Da li ste sigurni?"]="Are you shure?";
	en["Ne možete menjati zaključan zapis!"]="You can not edit locked record!";
	d["en"]=en;
	
	var sr={};
	sr[ulaz]=ulaz;
	d["sr"]=sr;
	
	if(d[locale][ulaz])
		return d[locale][ulaz];
	else
		return ulaz;
}