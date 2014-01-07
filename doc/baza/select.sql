# kategorije od uporabnika z id-jem
select ID_category,name,color from category where ID_user=1;

# taski uporabnika s 1. kategorije
select task.ID_task, task.title from task, user, category where
task.ID_user=user.ID_user and user.ID_user=category.ID_user and 
category.ID_category=task.ID_category
and category.ID_category=1;

# taski uporabnika z 2. kategorije
select task.ID_task, task.title from task, user, category where
task.ID_user=user.ID_user and user.ID_user=category.ID_user and 
category.ID_category=task.ID_category
and category.ID_category=2;

# taski uporabnika z imenom kategorije "freaky tasks"
select task.ID_task, task.title from task, user, category where
task.ID_user=user.ID_user and user.ID_user=category.ID_user and 
category.ID_category=task.ID_category
and category.name="freaky tasks";

#taski vseh uporabnikov
select task.ID_task, task.title, user.username from task, user, category where
task.ID_user=user.ID_user and user.ID_user=category.ID_user and 
category.ID_category=task.ID_category;

#taski uporabnika z uporabniškim imenom stefka in geslom zmigavc
select task.ID_task, task.title, user.username from task, user, category where
task.ID_user=user.ID_user and user.ID_user=category.ID_user and 
category.ID_category=task.ID_category and
user.username="stefka" and user.password="zmigavc";

#taski uporabnika z uporabniškim imenom zmago in geslom batina
select task.ID_task, task.title, user.username from task, user, category where
task.ID_user=user.ID_user and user.ID_user=category.ID_user and 
category.ID_category=task.ID_category and
user.username="zmago" and user.password="batina";


