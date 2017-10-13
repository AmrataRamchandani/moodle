*******PAGE UNDER CONSTRUCTION********

Moodle Plugin - User IP Mappings

Background and Need ->

1)If we want to restrict the attempt of a quiz by the students only from the IP address alloted to them
to increase the strictness or to have a record of details from where all the quiz was attempted and by whom etc.

Solution ->

We will assign each student one IP address and no two students can have same IP address at the same time.

Installation ->

1) Download and unpack the plugin.
2) Place the plugin folder in the " /mod/quiz/accessrule " subdirectory.
3) Visit http://yoursite.com/admin to finish the installation
4) Complete the installation by clicking on “Upgrade Moodle database now”,click on continue after the success 
notification appears on the page.

Usage ->

To use this plugin,follow below two steps.
1)Enable "Use Student-IP Mappings for attempting the quiz",in Quiz Settings(it can be done while creating the quiz
or after creating,by visiting Edit Settings)
2)Check "Allow Unmapped" if you want to allow the unmapped students to attempt the quiz.If left unchecked,students with no ip address
assigned would not be allowed to attempt the quiz.In short,
Yes -> Allow all,Deny some.
No -> Deny all,Allow some.
3)Upload Student-IP Mappings by visiting Quiz->Edit Settings->Manage Student-IP Mappings->Import Student-IP Mappings.
4)For each quiz,this functionality is enabled,mappings should be uploaded separately for every quiz.
A quiz can have multiple IP mappings uploaded,though only the latest mapping would be considered to limit the student from
attempting the quiz.

Requirement of the mapping file to be uploaded are:

i)The file should be in CSV(comma separated values) format.
ii)It should have two fields/columns.First column should be for usernames and Second for ip addresses.
iii)Both the fields(username,ip) are required field,if even any one of the field is missing would lead to an error and the upload 
wouldn't proceed untill all the required fields are present in the csv file.
iv)There should be one to one mapping between student and ip address,i.e each Student should have only one IP Address allotted 
(in multiple mapping uploads,previous alloted ip would override with the latest one by default.)
and also each IP Address should be alloted to only one student at the same time(in multiple mapping uploads,if a ip address is already
alloted to some student in previous uploads then it would through an error and we****
Pre-checks would be done to avoid the conflicting mappings.
v)


