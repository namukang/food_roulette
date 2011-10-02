#!/usr/bin/python2.6

import cgi
import Ordrin
import site_vars

print "Content-type: text/html\r\n"

email = str(form.getfirst("email", ""))
password = str(form.getfirst("password", ""))

if email and password:
  Ordrin.api.initialize(site_vars._api_key, site_vars._u_url)
  Ordrin.api.initialize(site_vars._api_key, site_vars._r_url)
  Ordrin.api.setCurrAcct(email, password)
  place = Ordrin.u.getAddress()
  when = Ordrin.dTime.now()
  when.asap()
  content = Ordrin.r.deliveryList(when, place)
  print "<message><content>%s</content></message>" % content

else:
  print '<query>%s login</query>' % site_vars._site_name
  
