%define _topdir /usr/src/redhat
%define debug_packages %{nil}
%define debug_package %{nil}

Summary: Rhea System Module
Name: rhea
Version: 1.1
Release: rhel
License: Commercial
Source: %{name}-%{version}.tar.gz
Vendor: Beijing eYou Information Technology Co., Ltd.
Group: Applications/Productivity
BuildArch: noarch
Packager: eYou Elephant Team
Buildroot: %{_tmppath}/%{name}-%{version}-root
Prefix: /usr/local/rhea

%description
Rhea System Module
add password logic

%prep
# prep section

%setup
# setup section

%build
# build section

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{prefix}
cp -a * $RPM_BUILD_ROOT%{prefix}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%{prefix}

%pre
# pre section

%post
# post section
chown -R eyou:eyou %{prefix}/{log,run,etc,data,tmp} 
ln -sf %{prefix}/sbin/rhea /usr/bin/rhea
ln -sf %{prefix}/sbin/rhea /etc/init.d/rhea
chkconfig rhea on

%preun
# preun section
chkconfig rhea off

%postun
# postun section
rm -fr /usr/bin/rhea
rm -fr /etc/init.d/rhea
rm -fr %{prefix}
