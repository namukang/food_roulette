#!/usr/bin/python2.6

import cgi
import Ordrin
import hashlib
import site_vars

form = cgi.FieldStorage()

temp_email = str(form.getfirst("temp_email"))
temp_password = str(form.getfirst("temp_password"))

print "Content-type: text/html\r\n"

email = str(form.getfirst("email", ""))
password = str(form.getfirst("password", ""))
first_name = str(form.getfirst("first_name", ""))
last_name = str(form.getfirst("last_name", ""))
# Uncomment line below to reset session variables
# (email, password, first_name, last_name) = (None, None, None, None)

if email and password:
  content = "%s, you are already logged in as %s." % (first_name, email)
else:
  Ordrin.api.initialize(site_vars._api_key, site_vars._u_url)
  Ordrin.api.setCurrAcct(temp_email, temp_password)
  info = Ordrin.u.getAcct()
  if not Ordrin.api._errs and info:
    first_name = info['first_name']
    last_name = info['last_name']
    email = info['em']
    password = temp_password
    content = "%s, you have been logged in as %s." % (first_name, email)
  else:
    content = "Unable to log in as %s. Please try again." % temp_email

cmrl_res = { "email" : email,
             "password" : password,
             "first_name" : first_name,
             "last_name" : last_name,
             "content" : content }

print """
<block>
<set name="email">%(email)s</set>
<set name="password">%(password)s</set>
<set name="first_name">%(first_name)s</set>
<set name="last_name">%(last_name)s</set>
<message><content>%(content)s</content></message>
</block>
""" % cmrl_res
